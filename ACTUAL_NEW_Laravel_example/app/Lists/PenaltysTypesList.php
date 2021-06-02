<?php


namespace App\Lists;


class PenaltysTypesList
{
    public static function getList()
    {
        return  [
            'shtraf' => __('lists.penaltysTypesList.shtraf'),
            'doplata' => __('lists.penaltysTypesList.doplata'),
        ];
    }
}
