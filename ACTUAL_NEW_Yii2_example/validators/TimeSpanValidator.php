<?php

namespace app\validators;

use yii;
use yii\validators\Validator;

/**
 * Class TimeSpanValidator
 * @package app\validators
 *
 * Класс валидации временного промежутка
 */
class TimeSpanValidator extends Validator
{
    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $error = null;

        if ($value !== null && !preg_match('/(([\d]+)д)?(([\d]+)ч)?(([\d]+)м)?(([\d]+)с)?/ui', $value, $m)) {
            $error = '{attribute} надо вводить с указанием едениц изменения времен (Например "1д 12ч 30м 15с" или "3 ч." или "1д. 10 м.")';
        }
        return !$error ? null : [$error, []];
    }


    /**
     * @param int $value
     * @return string
     */
    public static function integer2timeSpan($value) {
        $result = '';
        $value = intval($value, 10);
        if ($value >= 3600) {
            if ($value >= 86400) {
                $rest   = $value % 86400;
                $num    = ($value - $rest)/86400;
                if ($num) {
                    $result .= $num;
                    $result .= ' д. ';
                }
                $value = $rest;
            }
            $rest   = $value % 3600;
            $num    = ($value - $rest)/3600;
            if ($num) {
                $result .= $num;
                $result .= ' ч. ';
            }
            $value = $rest;
        }
        if ($value) {
            $result .= round($value / 60);
            $result .= ' мин.';
        }
        return $result;
    }

    /**
     * @param string $value
     * @return int
     */
    public static function timeSpan2integer($value) {
        $value = preg_replace('/[\s\.]+/', "", $value);

        preg_match_all('/(([\d]+)д)?(([\d]+)ч)?(([\d]+)м)?(([\d]+)с)?/', $value, $matches);

        if (isset($matches[0])) {
            $days       = intval($matches[2] ? $matches[2][0] : 0, 10);
            $hours      = intval($matches[2] ? $matches[4][0] : 0, 10);
            $minutes    = intval($matches[2] ? $matches[6][0] : 0, 10);
            $seconds    = intval($matches[2] ? $matches[8][0] : 0, 10);
            $time       = $days * 86400 + $hours * 3600 + $minutes * 60 + $seconds;
            return $time;
        }

        return 0;
    }
}