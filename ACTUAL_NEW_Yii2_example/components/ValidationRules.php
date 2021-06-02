<?php
namespace app\components;

use app\validators\EnumValidator;
use app\validators\UuidValidator;

class ValidationRules
{
    /**
     * @param string $field
     * @param string $className
     * @param array $extend
     * @param string $targetField
     * @return array
     */
    public static function ruleFk($field, $className, $extend = [], $targetField = 'id') {
        $r =  [
            [
                [$field],
                'exist',
                'skipOnError'       => true,
                'targetClass'       => $className,
                'targetAttribute'   => $targetField,
                'allowArray'        => false,
            ],
        ];

        if (!empty($extend)) {
            foreach ($r as $k => $v) {
                $r[$k] = array_merge($r[$k], $extend);
            }
        }
        return $r;
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @param array $extend
     * @return array
     */
    public static function ruleDefault($field, $value, $extend = []) {
        if (!is_array($field))  {
            $field = [$field];
        }
        $r = [
            [
                $field,
                'default',
                'value'     => $value,
            ]
        ];

        if (!empty($extend)) {
            foreach ($r as $k => $v) {
                $r[$k] = array_merge($r[$k], $extend);
            }
        }
        return $r;
    }

    /**
     * @param string $field
     * @param string $className
     * @param array $extend
     * @return array
     */
    public static function ruleEnum($field, $className, $extend = []) {
        $r = [
            [
                [$field],
                'number',
                'integerOnly'   => true,
            ],
            [
                [$field],
                EnumValidator::className(),
                'targetClass'   => $className,
            ],
            [
                [$field],
                EnumValidator::className(),
                'targetClass'   => $className,
                'allowArray'    => true,
            ]
        ];
        if (!empty($extend)) {
            foreach ($r as $k => $v) {
                $r[$k] = array_merge($r[$k], $extend);
            }
        }
        return $r;
    }

    /**
     * @param string $field
     * @return array
     */
    public static function ruleUuid($field = 'id') {
        $field = is_array($field) ? $field : [$field];
        return [
            [
                $field,
                UuidValidator::className(),
            ],
            [
                $field,
                UuidValidator::className(),
                'allowArray'    => true,
            ],
        ];
    }

    /**
     * @param mixed $fields,... массив полей или несколько параметров
     * @return array
     */
    public static function ruleDateTime($fields) {
        $fields = func_get_args();
        if (count($fields) == 1 && is_array($fields[0])) {
            $fields = $fields[0];
        }
        return [
            [$fields, 'date', 'format' => 'php:' . DateTime::DB_DATETIME_FORMAT],
        ];
    }


    /**
     * @param mixed $fields,... массив полей или несколько параметров
     * @param $extend
     * @return array
     */
    public static function ruleRequired($fields, $extend = []) {
        $fields = func_get_args();
        if (count($fields) == 1 && is_array($fields[0])) {
            $fields = $fields[0];
        }
        if (count($fields) > 1) {
            $last = end($fields);
            if (is_array($last)) {
                $extend = array_pop($fields);
            } else {
                $extend = [];
            }
        }
        $r = [
            [
                $fields, 'required'
            ]
        ];
        if (!empty($extend)) {
            foreach ($r as $k => $v) {
                $r[$k] = array_merge($r[$k], $extend);
            }
        }
        return $r;
    }

}