<?php

namespace app\validators;


use yii;
use yii\db\BaseActiveRecord;


class DateValidator extends \yii\validators\DateValidator
{
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if ($model instanceof BaseActiveRecord) {
            $value = $model->getAttribute($attribute);
        } else {
            $value = $model->$attribute;
        }
        $timestamp = $this->parseDateValue($value);
        if ($timestamp === false) {
            $this->addError($model, $attribute, $this->message, []);
        } elseif ($this->timestampAttribute !== null) {
            $model->{$this->timestampAttribute} = $timestamp;
        }
    }
}
