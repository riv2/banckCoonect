<?php

namespace frontend\services\traits;

use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Содержит общие методы для работы с формами.
 * Class FormTrait
 */
trait FormTrait
{
    /**
     * Заполнить форму ошибками.
     * @param Model $form
     * @param mixed $errors
     */
    protected function populateFormErrors(Model $form, $errors): void
    {
        if (!is_array($errors)) {
            return;
        }

        foreach ($errors as $error) {
            $form->addError(ArrayHelper::getValue($error, 'field'), ArrayHelper::getValue($error, 'message'));
        }
    }
}
