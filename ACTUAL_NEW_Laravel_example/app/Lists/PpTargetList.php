<?php

namespace App\Lists;


class PpTargetList
{
    public static function getList()
    {
        return  [
            'lead'=>__('lists.PpTargetList.services'),
            'products'=>__('lists.PpTargetList.goods'),
        ];
    }

}
