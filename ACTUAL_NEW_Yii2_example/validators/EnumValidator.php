<?php

namespace app\validators;

use app\components\base\type\Enum;
use yii;
use yii\base\InvalidParamException;
use yii\validators\Validator;

class EnumValidator extends Validator
{
    /** @var  Enum */
    public $targetClass;

    /** @var  bool */
    public $allowArray = false;

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $error = null;
        $class_name = $this->targetClass;

        /** @var Enum $class_name */
        if (!$class_name) {
            throw new InvalidParamException('Не указан класс');
        } else{
            if ($this->allowArray && is_array($value)) {
                foreach ($value as $val) {
                    if ($val === "" || $val === null || !$class_name::getNameById($val, false)) {
                        $error = 'Одно из значений не верно: "' . $val . '". Такая запись не существует. ';
                    }
                }
            } else {
                if ($value === "" || $value === null || !$class_name::getNameById($value, false)) {
                    $error = 'Не верное значение "' . $value . '" Такая запись не существует';
                }
            }
        }

        return !$error ? null : [$error, []];
    }
}
