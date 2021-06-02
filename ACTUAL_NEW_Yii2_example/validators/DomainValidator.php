<?php

namespace app\validators;

use yii;
use yii\validators\Validator;

/**
 * Class DomainValidator
 * @package app\validators
 *
 * Класс валидации UUID
 */
class DomainValidator extends Validator
{
    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $error      = null;
        $errorMsg   = "{attribute} значение [$value] не похоже на домен.";

        if (!self::test($value)) {
            $error = $errorMsg;
        }

        return !$error ? null : [$error, []];
    }

    public static function test($domain_name) {
        return (preg_match("/^([a-za-я\d](-*[a-za-я\d])*)(\.([a-za-я\d](-*[a-za-я\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
    }
}