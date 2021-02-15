<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.12.18
 * Time: 11:45
 */

namespace App\Services;


class ImportMirasFull
{
    /**
     * @param $value
     * @return int
     */
    static function transformGender($value)
    {
        return $value === 'MALE' ? 1 : 0;
    }

    static function transformDocType($value)
    {
        return $value === 'PASSPORT' ? 1 : 0;
    }

    /**
     * @param $value
     * @return false|string
     */
    static function transformDate($value)
    {
        return $value ? date('Y-m-d', strtotime($value)) : null;
    }
}