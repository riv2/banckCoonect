<?php
/**
 * User: dadicc
 * Date: 30.09.19
 * Time: 14:46
 */

namespace App\Services;

class PhoneService
{

    /**
     * @param $sNumber
     * @return mixed|string
     */
    public static function getNormolizeNumber($sNumber)
    {

        $sPhone = preg_replace("/[^0-9]/", '', $sNumber);
        if( strlen($sPhone) == 10 ) {
            $sPhone = '+7' . $sPhone;
        } elseif( strlen($sPhone) == 11 ){
            $sPhone = '+7' . substr($sPhone,1);
        } elseif( strlen($sPhone) == 12 ){
            $sPhone = '+' . $sPhone;
        }

        return $sPhone;

    }

}