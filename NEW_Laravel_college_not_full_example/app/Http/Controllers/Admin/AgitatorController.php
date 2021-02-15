<?php
/**
 * User: dadicc
 * Date: 2/4/20
 * Time: 4:20 PM
 */


namespace App\Http\Controllers\Admin;

use Auth;
use App\{AgitatorRefunds};
use App\Http\Controllers\Controller;
use App\Validators\{
    AdminAgitatorControllerAjaxChangeTransactionStatusValidator,
    AdminAgitatorControllerAjaxGetTransactionsValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log,Response};

class AgitatorController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function transactions( Request $request )
    {

        return view('admin.pages.agitator.transactions');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetTransactions( Request $request )
    {

        // validation data
        $obValidator = AdminAgitatorControllerAjaxGetTransactionsValidator::make( $request->all() );
        if( $obValidator->fails() || empty(Auth::user()->id) )
        {
            return Response::json([
                'status'  => false,
                'message' => __('Data not found')
            ]);
        }

        $iPage = ( $request->has('page') && ( intval($request->input('page')) > 0) ) ? intval($request->input('page')) : 1;

        $oAgitatorRefundsRequest = AgitatorRefunds::
        whereHas('user')->
        with('user.studentProfile')->
        whereHas('bank')->
        orderBy('id','desc');
        if( $request->has('search') && ( $request->input('search') != '' ) )
        {
            $val = $request->input('search');
            if( strlen( intval($val) ) > 1 )
            {
                $oAgitatorRefundsRequest->
                whereHas('user', function($query) use ($val) {
                    $query->where('id',$val);
                });
            } else {
                $oAgitatorRefundsRequest->
                whereHas('user.studentProfile', function($query) use ($val) {
                    $query->where('fio','like','%'.$val.'%');
                });
            }
        }
        $oAgitatorRefunds = $oAgitatorRefundsRequest->paginate(10, ['*'], 'page', $iPage);
        if( !empty($oAgitatorRefunds) )
        {
            $oAgitatorRefunds->getCollection()->transform(function (&$value) {

                if( !empty($value->user) )
                {
                    $value->user_info = "id: " . $value->user->id;
                }
                if( !empty($value->user->studentProfile) )
                {
                    $value->user_info .= " ; иин: " . $value->user->studentProfile->iin;
                } else {
                    $value->user->studentProfile->fio = $value->user->name;
                }
                if( !empty($value->bank) )
                {
                    $value->bank_info = "id: " . $value->bank->id . " ; бик: " . $value->bank->bic;
                }
                $value->date = date('Y-m-d H:i',strtotime($value->created_at));
                return $value;
            });
        }

        return Response::json([
            'status'  => true,
            'message' => __('Success'),
            'models'  => $oAgitatorRefunds
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxChangeTransactionStatus( Request $request )
    {

        // validation data
        $obValidator = AdminAgitatorControllerAjaxChangeTransactionStatusValidator::make( $request->all() );
        if( $obValidator->fails() || empty(Auth::user()->id) )
        {
            return Response::json([
                'status'  => false,
                'message' => __('Data not found')
            ]);
        }

        $oAgitatorRefunds = AgitatorRefunds::
        where('id',$request->input('transaction'))->
        first();

        if( !empty($oAgitatorRefunds) && in_array(
            $request->input('status'),
            [AgitatorRefunds::STATUS_PROCESS,AgitatorRefunds::STATUS_SUCCESS,AgitatorRefunds::STATUS_ERROR,AgitatorRefunds::STATUS_CANCELLED]
            ) )
        {
            $oAgitatorRefunds->status = $request->input('status');
            $oAgitatorRefunds->save();
        }

        return Response::json([
            'status'  => true,
            'message' => __('Transaction status changed successfully')
        ]);

    }


}