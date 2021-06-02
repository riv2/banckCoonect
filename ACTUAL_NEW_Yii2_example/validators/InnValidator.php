<?php

namespace app\validators;

use yii;
use yii\validators\Validator;

class InnValidator extends Validator
{
    /** @var  bool юридичесский */
    public $is_juristic;

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $error = null;
        if ($this->is_juristic) {
            if (!preg_match('/^\d{10}$/', $value)) {
                $error = 'Для юридических лиц ИНН содержать 10 цифр';
            } elseif (preg_match('/\b(\d)\1{9}\b/', $value)) {
                $error = 'ИНН не может состоять из одинаковых символов';
            } else {
                if ($value{9} != ((2*$value{0} + 4*$value{1} + 10*$value{2} + 3*$value{3} + 5*$value{4} + 9*$value{5} + 4*$value{6} + 6*$value{7} + 8*$value{8}) % 11) % 10) {
                    $error = 'ИНН не прошел вычисление контрольных цифр';
                }
            }
        } else {
            if (!preg_match('/^\d{12}$/', $value)) {
                $error = 'Для физических лиц ИНН должен содержать 12 цифр';
            }  elseif (preg_match('/\b(\d)\1{11}\b/', $value)) {
                $error = 'ИНН не может состоять из одинаковых символов';
            } else {
                if ($value{10} != ((7*$value{0} + 2*$value{1} + 4*$value{2} + 10*$value{3} + 3*$value{4} + 5*$value{5} + 9*$value{6} + 4*$value{7} + 6*$value{8} + 8*$value{9}) % 11) % 10) {
                    $error = 'ИНН не прошел вычисление контрольных цифр (11)';
                } elseif ($value{11} != ((3*$value{0} + 7*$value{1} + 2*$value{2} + 4*$value{3} + 10*$value{4} + 3*$value{5} + 5*$value{6} + 9*$value{7} + 4*$value{8} + 6*$value{9} + 8*$value{10}) % 11) % 10) {
                    $error = 'ИНН не прошел вычисление контрольных цифр (12)';
                }
            }
        }

        return !$error ? null : [$error, []];
    }
}
