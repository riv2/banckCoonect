<?php
/**
 * User: dadicc
 * Date: 2/18/20
 * Time: 2:28 PM
 */

namespace App\Services;

use Auth;
use App\{
    UserBank,
    UserBusiness
};

class AgitatorHelper
{

    /**
     * save UserBusiness
     * @param array $aYrData
     * @return
     */
    public static function saveUserBusiness( $aYrData )
    {

        $oUserBusiness = UserBusiness::
        where('user_id',Auth::user()->id)->
        whereNull('deleted_at')->
        first();

        if( empty($oUserBusiness) )
        {
            $oUserBusiness = new UserBusiness();
        }

        $oUserBusiness->fill($aYrData);
        $oUserBusiness->user_id = Auth::user()->id;
        $oUserBusiness->save();

    }

    /**
     * save user bank
     * @param int $iBankId
     * @param string $sIban
     * @return
     */
    public static function saveUserBank( $iBankId, $sIban )
    {

        $oUserBank = UserBank::
        where('user_id',Auth::user()->id)->
        where('bank_id',$iBankId)->
        first();

        if( empty($oUserBank) )
        {
            $oUserBank = new UserBank();
        }

        $sIban = str_replace(['KZ','kz'],'',$sIban);

        $oUserBank->fill([
            'user_id' => Auth::user()->id,
            'bank_id' => $iBankId,
            'iban'    => $sIban,
        ]);
        $oUserBank->save();

    }


}