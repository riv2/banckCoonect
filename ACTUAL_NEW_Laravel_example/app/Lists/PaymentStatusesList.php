<?php
/**
 * Project qpartners
 * Created by danila 23.06.2020 @ 15:55
 */

namespace App\Lists;


class PaymentStatusesList
{
    static function getList()
    {
        return [
            0=>"Ожидает",
            1=>"Оплачен",
            2=>"Ожидает подтверждения"
        ];
    }
}
