<?php

namespace api\filters;

use yii\filters\auth\HttpBearerAuth as BaseHttpBearerAuth;
use yii\helpers\StringHelper;
use Yii;

/**
 * Расширенный фильтр Bearer аутентификации.
 * Добавлены дополнительные свойства для ограничения действия фильтра в зависимости от типа HTTP запроса.
 */
class HttpBearerAuth extends BaseHttpBearerAuth
{
    /**
     * @var array Методы HTTP запросов, на которые действует фильтр.
     */
    public $onlyMethods;

    /**
     * @var array Методы HTTP запросов, на которые не действует фильтр.
     */
    public $exceptMethods = [];

    /**
     * @inheritdoc
     */
    protected function isActive($action)
    {
        $id = $this->getActionId($action);
        $requestMethod = ($this->request ?: Yii::$app->getRequest())->getMethod();

        if (empty($this->only)) {
            $onlyMatch = true;
        } else {
            $onlyMatch = false;
            foreach ($this->only as $pattern) {
                if (StringHelper::matchWildcard($pattern, $id)) {
                    $onlyMatch = true;
                    break;
                }
            }
        }

        if (empty($this->onlyMethods)) {
            $onlyMethodsMatch = true;
        } else {
            $onlyMethodsMatch = false;
            if (\in_array(
                mb_strtolower($requestMethod, 'utf-8'),
                array_map('mb_strtolower', $this->onlyMethods, array_fill(0, \count($this->onlyMethods), 'utf-8')),
                true
            )) {
                $onlyMethodsMatch = true;
            }
        }

        $exceptMatch = false;
        foreach ($this->except as $pattern) {
            if (StringHelper::matchWildcard($pattern, $id)) {
                $exceptMatch = true;
                break;
            }
        }

        $exceptMethodsMatch = false;
        if (\in_array(
            mb_strtolower($requestMethod, 'utf-8'),
            array_map('mb_strtolower', $this->exceptMethods, array_fill(0, \count($this->exceptMethods), 'utf-8')),
            true
        )) {
            $exceptMethodsMatch = true;
        }

        return !$exceptMatch && $onlyMatch && !$exceptMethodsMatch && $onlyMethodsMatch;
    }
}
