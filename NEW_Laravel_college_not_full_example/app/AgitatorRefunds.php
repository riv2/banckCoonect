<?php
/**
 * User: dadicc
 * Date: 1/12/20
 * Time: 7:38 PM
 */

namespace App;

use Auth;
use App\{AgitatorUsers,UserBusiness};
use App\Services\{DocxHelper,PhpOfficeHelper};
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{Log,Mail};

class AgitatorRefunds extends Model
{

    use SoftDeletes;

    // status
    const STATUS_PROCESS    = "process";
    const STATUS_SUCCESS    = "success";
    const STATUS_CANCELLED  = "cancelled";
    const STATUS_ERROR      = "error";

    const AGITATOR_WITHDRAW_PERCENT = 21;

    protected $table = 'agitator_refunds';

    protected $fillable = [
        'user_id',
        'bank_id',
        'iban',
        'percent',
        'order_number',
        'cost',
        'status'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bank()
    {
        return $this->hasOne('App\Bank', 'id', 'bank_id');
    }


    /**
     * @param $iMultiPlicity
     * @param $oBank
     * @param $iban
     * @param $iCost
     * @param $iWithdrawPercent
     * @param $iWithdrawAmount
     * @return bool
     */
    public static function sendMail( $iMultiPlicity, $oBank, $iban, $iCost, $iWithdrawPercent, $iWithdrawAmount )
    {

        $bResponse = true;

        try {


            // находим студентов для этого агитатора
            $aUsers = [];
            $oAgitatorUsers = AgitatorUsers::
            whereHas('stud')->
            whereHas('stud.studentProfile')->
            where('user_id',Auth::user()->id)->
            where('status',AgitatorUsers::STATUS_OK)->
            orderBy('created_at','asc')->
            limit( $iMultiPlicity )->
            get();

            if( !empty($oAgitatorUsers) && (count($oAgitatorUsers) > 0) )
            {
                foreach($oAgitatorUsers as $itemAU)
                {
                    $aUsers[] = $itemAU->stud->studentProfile->fio . ' ('.$itemAU->stud->studentProfile->iin.')';
                    $itemAU->status = AgitatorUsers::STATUS_PAYED;
                    $itemAU->save();
                }
            }

            $oUser = Auth::user();
            $oStudentProfile = Auth::user()->studentProfile;

            $sFirmFIo = '';
            $sBinIin  = '';
            // определяем ИП или ФИО
            $oUserBusiness = UserBusiness::
            where('user_id',Auth::user()->id)->
            whereNull('deleted_at')->
            first();
            if( !empty($oUserBusiness) )
            {

                $sFirmFIo .= $oUserBusiness->name . ', ';
                $sFirmFIo .= 'БИН ' . $oUserBusiness->bin . ', ';
                $sFirmFIo .= $oUserBusiness->adress . ', ';
                $sFirmFIo .= 'тел: ' . $oUserBusiness->phone;
                $sBinIin   = $oUserBusiness->bin;
            } else {

                $sFirmFIo .= $oStudentProfile->fio . ' ';
                $sFirmFIo .= 'тел: ' . $oStudentProfile->mobile;
                $sBinIin   = $oStudentProfile->iin;
            }


            // генерация акта
            $currentLocale = app()->getLocale();
            app()->setLocale('ru');
            app()->setLocale($currentLocale);

            $actParams = [
                '${t_firmfio}'      => $sFirmFIo,
                '${t_biniin}'       => $sBinIin,
                '${t_cost}'         => 5000,
                '${t_total}'        => intval( 5000 * count( $aUsers ) ),
                '${t_users_count}'  => count( $aUsers ),
                '${t_date}'         => date('d.m.Y'),
                '${t_number}'       => '09/713-5, 05.01.2020',
                '${t_application}'  => 'Приложение к акту выполненных работ от ' . date('d.m.Y')
            ];
            $actFile = DocxHelper::replace(resource_path('docx/act_vp.docx'), $actParams, 'docx');
            $actFile = PhpOfficeHelper::addTableForProfileNoteOpisList($actFile,$aUsers);

            //Log::info('file: ' . var_export($actFile,true));

            // front id
            $oProfileDocFrontId = ProfileDoc::
            where('user_id',Auth::user()->id)->
            where('doc_type',ProfileDoc::TYPE_FRONT_ID)->
            where('last',1)->
            orderBy('created_at','desc')->
            first();

            // back id
            $oProfileDocBackId = ProfileDoc::
            where('user_id',Auth::user()->id)->
            where('doc_type',ProfileDoc::TYPE_BACK_ID)->
            where('last',1)->
            orderBy('created_at','desc')->
            first();

            // отправка репорта на почту
            Mail::send('emails.agitator_refund', [
                'user'           => $oUser,
                'bank'           => $oBank,
                'iban'           => $iban,
                'cost'           => $iCost,
                'percent'        => $iWithdrawPercent,
                'withdrawAmount' => $iWithdrawAmount
            ],
                function ($message) use ($oUser,$oProfileDocFrontId,$oProfileDocBackId,$actFile) {
                    $message->from(getcong('site_email'), getcong('site_name'));
                    $message->to( explode(',',env('EMAIL_AGITATOR_REFUND_INFORM')) )
                        ->subject('Вывод средств агитатором '. $oUser->studentProfile->fio .' '. $oUser->studentProfile->iin);

                    if( !empty($oProfileDocFrontId) )
                    {
                        $message->attach(
                            public_path('') . '/images/uploads/frontid/' . substr($oProfileDocFrontId->filename, 0,1) . '/' .  $oProfileDocFrontId->filename . ProfileDoc::EXT,[
                                'as' => $oProfileDocFrontId->filename . ProfileDoc::EXT, 'mime' => 'image/jpg'
                            ]
                        );
                    }
                    if( !empty($oProfileDocBackId) )
                    {
                        $message->attach(
                            public_path('') . '/images/uploads/backid/' . substr($oProfileDocBackId->filename, 0,1) . '/' .  $oProfileDocBackId->filename . ProfileDoc::EXT,[
                                'as' => $oProfileDocBackId->filename . ProfileDoc::EXT, 'mime' => 'image/jpg'
                            ]
                        );
                    }
                    if( !empty($actFile) )
                    {
                        $message->attach(
                            $actFile,[
                                'as' => 'act_vp.docx', 'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ]
                        );
                    }
                });
            unlink($actFile);


        } catch (\Exception $e) {
            $bResponse = false;
        }

        return $bResponse;

    }


}