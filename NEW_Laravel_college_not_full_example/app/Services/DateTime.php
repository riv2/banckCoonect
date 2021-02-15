<?php


namespace App\Services;


class DateTime
{

    /**
     * @param $monthNum
     * @return bool|mixed
     */
    static function getMonthNameByNum($monthNum)
    {
        if($monthNum > 12 || $monthNum < 1)
        {
            return false;
        }

        $months = [
            'январь',
            'февраль',
            'март',
            'апрель',
            'май',
            'июнь',
            'июль',
            'август',
            'сентябрь',
            'октябрь',
            'ноябрь',
            'декабрь'
        ];

        return $months[$monthNum - 1];
    }
}