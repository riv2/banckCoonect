<?php

namespace app\validators;

use yii;
use yii\validators\Validator;

/**
 * Class UuidValidator
 * @package app\validators
 *
 * Класс валидации UUID
 */
class UuidValidator extends Validator
{
    /** @var  bool */
    public $allowArray = false;


    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $error      = null;
        $errorMsg   = "{attribute} не соответствует UUID маске xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";

        if ($this->allowArray) {
            if (!is_array($value) && !empty($value)) {
                if (strpos($value, ',') !== FALSE) {
                    $value = explode(',', $value);
                }
            }
            if (is_array($value)) {
                foreach ($value as $val) {
                    if (!self::test($val)) {
                        $error = $errorMsg;
                    }
                }
                return !$error ? null : [$error, []];
            }
        }

        if (!self::test($value)) {
            $error = $errorMsg;
        }

        return !$error ? null : [$error, []];
    }

    public static function test($value) {
        if ($value !== null && !preg_match('/^([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})$/', $value, $m)) {
            return false;
        }
        return true;
    }
}