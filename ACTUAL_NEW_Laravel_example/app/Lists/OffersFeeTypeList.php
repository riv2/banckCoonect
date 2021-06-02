<?php
namespace App\Lists;

class OffersFeeTypeList
{
    static function getFeeTypeList()
    {
        return  [
            "fix" => "Фиксированная сумма",
            "share" => "Процент от суммы",
        ];
    }
}
