<?php

namespace App\Lists;


class UsersTypesList
{
    public static function getList()
    {
        return  [
        "partner"=>'Партнер',
        "manager"=>'Менеджер',
        "admin"=>'Администратор',
        "advertiser"=>'Рекламодатель'
    ];
    }

}
