<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-23
 * Time: 13:25
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Auth;
use App\Validators\{BuyServiceBuyWifiPackageValidator};
use App\Wifi;
use Illuminate\Http\Request;

class BuyServiceController extends Controller
{

    public function ajaxBuyWifiPackage( Request $request )
    {

        // validation data
        $obValidator = BuyServiceBuyWifiPackageValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('Error input data')
            ]);
        }


        // find package
        $oWifi = Wifi::
        where('code',$request->input('code'))->
        where('status',Wifi::STATUS_NEW)->
        whereNull('user_id')->
        whereNull('deleted_at')->
        first();
        if( empty($oWifi) )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('package already purchased')
            ]);
        }

        // TODO send request to 1C
        // ....

        // change status
        $oWifi->user_id = Auth::user()->id;
        $oWifi->status = Wifi::STATUS_ACTIVE;
        // TODO set Expire date
        $oWifi->save();

        return \Response::json([
            'status'  => true,
            'message' => __('Success')
        ]);


    }

}