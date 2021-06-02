<?php

namespace App\Lists;

class OrderStatusList
{
    static function getList(): array
    {
        return [
            'new',
            'sale',
            'reject'
        ];
    }
}
