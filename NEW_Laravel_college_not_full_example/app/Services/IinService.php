<?php


namespace App\Services;


class IinService
{
    const IIN_MAX_LENGTH = 12;

    /**
     * @param $iin
     * @return bool|string
     */
    static function normalize($iin)
    {
        $result = $iin;

        if(strlen($result) < self::IIN_MAX_LENGTH)
        {
            while (strlen($result) < self::IIN_MAX_LENGTH) {
                $result = '0' . $result;
            }
        }
        elseif (strlen($result) > self::IIN_MAX_LENGTH)
        {
            $result = substr($result, 0, self::IIN_MAX_LENGTH);
        }

        return $result;
    }
}