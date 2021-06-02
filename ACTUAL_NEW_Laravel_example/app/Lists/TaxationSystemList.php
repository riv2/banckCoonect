<?php

namespace App\Lists;

class TaxationSystemList
{
    public static function getList()
    {
        return  [
            'OSN' => 'ОСН',
            'USN' => 'УСН',
            'ENVD' => 'ЕНВД',
        ];
    }
}
