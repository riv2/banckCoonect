<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16.04.19
 * Time: 13:31
 */

namespace App\Services;


use Carbon\Carbon;

class Education
{
    /**
     * @return int
     */
    static function getYear()
    {
        $now = Carbon::now();

        return $now->month >= 9 ? $now->year : $now->year - 1;
    }
}