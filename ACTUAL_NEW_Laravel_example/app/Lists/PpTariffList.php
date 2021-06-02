<?php

namespace App\Lists;

class PpTariffList
{
    public static function getList()
    {
        return  [
            'free' => 'Триал',
            'start' => 'Старт',
            'professional' => 'Профессионал',
        ];
    }
}
