<?php
/**
 * User: dadicc
 * Date: 22.08.19
 * Time: 15:14
 */

namespace App\Http\Controllers\Student;

use Auth;
use App\Http\Controllers\Controller;
use App\Services\Service1C;
use Illuminate\Support\Facades\{Log,Response};
use App\Validators\ProfileUpdateUserBalanceValidator;
use App\User;
use Illuminate\Http\Request;

class UpdateUserBalanceController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetBalance( Request $request )
    {

        $oUser = User::
        with('studentProfile')->
        where('id',Auth::user()->id)->
        first();

        if( !empty($oUser) && !empty($oUser->studentProfile) )
        {

            // get balance by user iin from 1C
            $mResponse = Service1C::getBalance( $oUser->studentProfile->iin );
            if( !empty($mResponse) )
            {

                $oUser->balance = $mResponse;
                $oUser->save();

                return \Response::json([
                    'status'  => true,
                    'message' => __('Success'),
                    'user'    => $oUser->id,
                    'balance' => $mResponse
                ]);
            }
        }

        return \Response::json([
            'status'   => false,
            'message'  => __('Request error')
        ]);

    }

}