<?php

namespace App\Lists;


class PartnerStatusesList
{
    public static function getList()
    {
        return  [
        0=>'Подтверждение E-mail',
        1=>'Активен',
        2=>'Заблокирован',
        3=>'На модерации'
    ];
    }

}