<?php
/**
 * Project qpartners
 * Created by danila 25.06.2020 @ 13:55
 */

namespace App\Helpers;


class ArrayHelper
{
    static function getRandomValue($array)
    {
        shuffle($array);
        return $array[0];
    }
    static function getRandomKeyValue($array)
    {
        $array = array_keys($array);
        shuffle($array);
        return $array[0];
    }
}
