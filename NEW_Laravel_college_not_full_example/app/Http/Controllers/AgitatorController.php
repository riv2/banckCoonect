<?php
/**
 * User: dadicc
 * Date: 1/15/20
 * Time: 2:09 PM
 */

namespace App\Http\Controllers;

use App\UserBusiness;
use Auth;
use App\{
    AgitatorRefunds,
    AgitatorUsers,
    Bank,
    Nationality,
    ProfileDoc,
    Profiles,
    Role,
    User,
    UserBank
};
use App\Services\{DocxHelper,Service1C};
use App\Validators\{
    AgitatorControllerGetWithdrawInfoValidator,
    AgitatorControllerSendWithdrawRequesValidatort,
    AgitatorRegisterControllerProfileSaveImageValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{File,Image,Log,Mail,Response,Session,Storage};
use Illuminate\Support\{Str};

class AgitatorController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index( Request $request )
    {

        $oBank = Bank::
        whereNUll('deleted_at')->
        get();

        return view('agitator.index',[
            'banks' => $oBank
        ]);

    }


    /**
     * load profile photo
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileLoadData( Request $request )
    {

        if( empty(Auth::user()->id) || empty(Auth::user()->studentProfile) )
        {
            return Response::json([
                'status'  => false,
                'message' => __('Data not found')
            ]);
        }

        // load iban
        $oUserBank = UserBank::
        where('user_id',Auth::user()->id)->
        whereNull('deleted_at')->
        first();

        // user business
        $oUserBusiness = UserBusiness::
        where('user_id',Auth::user()->id)->
        whereNull('deleted_at')->
        first();

        return Response::json([
            'status'                    => true,
            'message'                   => __('Success'),
            'image'                     => Auth::user()->studentProfile->faceimg ?? false,
            'studentProfile'            => Auth::user()->studentProfile,
            'userBank'                  => $oUserBank,
            'userBalance'               => Auth::user()->balance ?? 0,                               // сумма на балансе
            'agitatorBalance'           => Auth::user()->agitatorFullBalance(),                      // общая агитаторская сумма для вывода
            'agitatorAvailableBalance'  => Auth::user()->agitatorAvailableBalance(),                 // сумма, доступная для снятия
            'userBusiness'              => $oUserBusiness                                            // данные Юр лица
        ]);

    }


    /**
     * save profile photo
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileSaveImage( Request $request )
    {

        // validation data
        $obValidator = AgitatorRegisterControllerProfileSaveImageValidator::make( $request->all() );
        if( $obValidator->fails() || empty(Auth::user()->studentProfile) )
        {
            return Response::json([
                'status'  => false,
                'message' => __('Data not found')
            ]);
        }

        $oProfile = Auth::user()->studentProfile;
        $oProfile->faceimg = $request->input('profileImage');
        $oProfile->saveProfilePhoto( $request->input('profileImgSource') );

        if($oProfile->save())
        {
            return Response::json([
                'status'  => true,
                'message' => __('Success')
            ]);
        }

        return Response::json([
            'status'  => false,
            'message' => __('Data not saved')
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetAgitatorUsersList( Request $request )
    {

        $searchParams = [];

        if ($request->has('columns')) {
            foreach ($request->input('columns') as $i => $column) {
                if (isset($column['search']['value']) && $column['search']['value'] != '') {
                    $searchParams[$i] = $column['search']['value'];
                }
            }
        }

        $searchData = AgitatorUsers::getList(
            Auth::user()->id,
            $request->input('search')['value'],
            $searchParams,
            $request->input('start'),
            $request->input('length'),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw'            => intval( $request->input('draw') ),
            'recordsTotal'    => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data'            => $searchData['data']
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgitatorUsersList( Request $request )
    {

        $iPage = ( $request->has('page') && ( intval($request->input('page')) > 0) ) ? intval($request->input('page')) : 1;

        $oAgitatorUsers = AgitatorUsers::
        with('stud')->
        with('stud.studentProfile')->
        where('user_id',Auth::user()->id)->
        //whereNotNull('cost')->
        paginate(10, ['*'], 'page',$iPage);

        //Log::info('data: ' . var_export($oAgitatorUsers,true));

        if( !empty($oAgitatorUsers) )
        {
            $oAgitatorUsers->getCollection()->transform(function (&$value) {

                if( !empty($value->stud) )
                {
                    $aStatusData = $value->stud->getAgitatorUserPayStatus();
                    if( !empty($aStatusData['status']) ){
                        $value->user_status = ( $aStatusData['status'] == true ) ? true : false;
                    }
                    if( !empty($aStatusData['message']) ){ $value->user_message = $aStatusData['message']; }
                }

                $value->status = __($value->status);

                return $value;
            });
        }

        return Response::json([
            'status'        => true,
            'message'       => __('Success'),
            'agitatorUsers' => $oAgitatorUsers
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgitatorTransactionHistory( Request $request )
    {

        $iPage = ( $request->has('page') && ( intval($request->input('page')) > 0) ) ? intval($request->input('page')) : 1;

        $oAgitatorRefunds = AgitatorRefunds::
        with('bank')->
        where('user_id',Auth::user()->id)->
        paginate(10, ['*'], 'page',$iPage);

        if( !empty($oAgitatorRefunds) )
        {
            $oAgitatorRefunds->getCollection()->transform(function (&$value) {

                $value->status = __($value->status);
                return $value;
            });
        }

        return Response::json([
            'status'                     => true,
            'message'                    => __('Success'),
            'agitatorTransactionHistory' => $oAgitatorRefunds
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendWithdrawRequest( Request $request )
    {

        // validation data
        $obValidator = AgitatorControllerSendWithdrawRequesValidatort::make( $request->all() );
        if( $obValidator->fails() )
        {
            return Response::json([
                'status'  => false,
                'message' => __('A data-entry error')
            ]);
        }

        // банковские данные
        $oBank = Bank::
        where('id',$request->input('bank_id'))->
        first();

        if( empty(Auth::user()->id) || empty(Auth::user()->studentProfile) || empty($oBank) )
        {
            return Response::json([
                'status'  => false,
                'message' => __('A data-entry error')

            ]);
        }

        $iCost = $request->input('cost');
        $iMultiPlicity = intval( $iCost / 5000 );

        // вычисляем сумму перевода и %
        $iCost            = $request->input('cost');
        $aWithdrawData    = Auth::user()->getWithdrawInfo( $request->input('cost') );
        $iWithdrawPercent = $aWithdrawData['amountPercent'];
        $iWithdrawAmount  = $aWithdrawData['amountWithdraw'];

        // создаем запись транзакцию и фиксируем вывод средств у агитатора
        $oAgitatorRefunds = new AgitatorRefunds();
        $oAgitatorRefunds->user_id       = Auth::user()->id;
        $oAgitatorRefunds->bank_id       = $oBank->id;
        $oAgitatorRefunds->iban          = str_replace(['KZ','kz'],'',$request->input('iban'));
        $oAgitatorRefunds->cost          = $iCost;
        $oAgitatorRefunds->percent       = $iWithdrawPercent;
        $oAgitatorRefunds->order_number  = Auth::user()->id . '_' . Str::random(40);
        $oAgitatorRefunds->status        = AgitatorRefunds::STATUS_PROCESS;
        if( $oAgitatorRefunds->save() )
        {

            // отправка на почту в бугалтерию
            AgitatorRefunds::sendMail(
                $iMultiPlicity,
                $oBank,
                str_replace(['KZ','kz'],'',$request->input('iban')),
                $iCost,
                $iWithdrawPercent,
                $iWithdrawAmount
            );

            if( !empty(Auth::user()->studentProfile->alien) )
            {

                // для не резедентов РК
                return Response::json([
                    'status'   => true,
                    'message'  => __('Your application is accepted. Please come within three working days to sign the certificate of completed work, with the original identity document, to the accounting Department of the University at 2 Sapak Datka street. The accounting Department is open from Monday to Friday from 9:00 to 18:00')
                ]);
            } else {

                return Response::json([
                    'status'   => true,
                    'message'  => __('Your application is accepted. Please come within three working days to sign the certificate of completed work to the accounting Department of the University at 2 Sapak Datka street. The accounting Department is open from Monday to Friday from 9:00 to 18:00')
                ]);
            }

        }


        return Response::json([
            'status'  => false,
            'message' => __('Refund error')
        ]);


    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithdrawInfo( Request $request )
    {

        // validation data
        $obValidator = AgitatorControllerGetWithdrawInfoValidator::make( $request->all() );
        if( $obValidator->fails() || empty(Auth::user()->id) )
        {
            return Response::json([
                'status'  => false,
                'message' => __('Data not found')
            ]);
        }

        $aData = Auth::user()->getWithdrawInfo( $request->input('cost') );

        return Response::json([
            'status'        => true,
            'message'       => __('Success'),
            'withdrawInfo'  => $aData
        ]);


    }


}