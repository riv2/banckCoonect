<?php
/**
 * User: dadicc
 * Date: 2/18/20
 * Time: 3:19 PM
 */

namespace App\Services;

use Auth;
use App\{
    Profiles
};

class RegistrationHelper
{

    /**
     * set registration step
     * @param string $sType
     * @param string $sStep
     * @return
     */
    public static function setRegistrationStep( $sType, $sStep )
    {

        if( !empty(Auth::user()) && !empty(Auth::user()->studentProfile) )
        {
            $oProfile = Auth::user()->studentProfile;
            $oProfile->$sType = $sStep;
            $oProfile->save();
        }

    }


}