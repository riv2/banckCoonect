<?php
/**
 * User: dadicc
 * Date: 03.10.19
 * Time: 20:10
 */

namespace App\Services;

use App\{FinanceNomenclature,StudentFinanceNomenclature,User, DiscountStudent, Semester, StudentDiscipline};
use Illuminate\Support\Facades\{DB,Log,Mail,Response};

class FinanceNomenclatureService
{


    public static function buy($iServiceId,$iUserId=null,$iCount=1)
    {

        if( empty($iUserId) ){

            $oUser = Auth::user();
        } else {

            $oUser = User::
            with('studentProfile')->
            where('id',$iUserId)->
            first();
        }

        /*
        $credit = DB::select("SELECT payed_credit_sum(".$oUser->id.") as credit_sum");
        if( empty($credit) ){
            $credit = 0;
        }
        */

        $semester = Semester::current($oUser->studentProfile->education_study_form);
        $semesterNumber = $oUser->studentProfile->currentSemester();
        $discount = DiscountStudent::getCreditPriceDiscount($oUser->id, $semester);
        $discount100 = $discount == 100 ? true:false;
        $oService = FinanceNomenclature::getById( $iServiceId );

        // calculationg how manu disciplines has been bought at current semester
        $boughtDisciplines = StudentDiscipline
                    ::where('student_id', $oUser->id)
                    ->where('payed', 1)
                    ->leftJoin('disciplines', 'disciplines.id', 'students_disciplines.discipline_id')
                    ->where('at_semester', $semesterNumber)
                    ->get();
        $creditsAtSemester = 0;
        foreach ($boughtDisciplines as $boughtDiscipline) {
            $creditsAtSemester += $boughtDiscipline->ects;
        }

        // balance limit - min value
        /* temporary disabled till 8.02.2020 
        if( ($oUser->balanceByDebt() < 500) || ($credit < 5) )
        */

        if( $iServiceId != 9 ) {
            if($creditsAtSemester < StudentDiscipline::MAX_CREDITS_AT_SEMESTER_FOR_REFERENCE && !$discount100) {
                return [
                        'status' => false,
                        'message' => __('You do not meet the requirements: minimum amount of credits must be 12')
                    ];
            } 
            /*
            if( ($oUser->balanceByDebt() < 30000) && !$discount100) {
                return \Response::json([
                    'status' => false,
                    'message' => __('You do not meet the requirements: the Amount on the balance must be more than 30000tg')
                ]);
            }
            */
        }


        $aReturn = [
            'status' => true,
            'message' => __('Success buy service')
        ];


        if (!empty($oUser->studentProfile) && !empty($oService)) {
            if ($oUser->balanceByDebt() < intval($oService->cost*$iCount) ) {
                return Response::json([
                    'status' => false,
                    'message' => __('Not enough funds on balance')
                ]);
            }

            // Limit 1
            if ($oService->only_one && StudentFinanceNomenclature::isBought($oUser->id, $oService->id)) {
                return Response::json([
                    'status' => false,
                    'message' => __('This service can only be bought once')
                ]);
            }

            // Limit 1 per semester
            if ($oService->only_one_per_semester && StudentFinanceNomenclature::isBought($oUser->id, $oService->id, $oUser->studentProfile->currentSemester())) {
                return Response::json([
                    'status' => false,
                    'message' => __('This service can only be bought once per semester')
                ]);
            }

            $balanceBeforeCall = $oUser->balance;

            $mResponse = Service1C::pay(
                $oUser->studentProfile->iin,
                $oService->code,
                intval($oService->cost*$iCount)
            );

            // Successfully paid
            if ($mResponse) {
                // Add log
                StudentFinanceNomenclature::add($oUser->id, $oService, $oUser->studentProfile->currentSemester(), $balanceBeforeCall);

                // print buy service
                $pdf = false;

                if ($oService->name_en == FinanceNomenclature::ENQUIRE_NAME_TR) {
                    $pdf = \App\Http\Controllers\Student\DocsController::genTranscript();
                } elseif ($oService->name_en == FinanceNomenclature::ENQUIRE_NAME_GCVP4) {
                    $pdf = \App\Http\Controllers\Student\DocsController::genGcvp4();
                } elseif ($oService->name_en == FinanceNomenclature::ENQUIRE_NAME_GCVP21) {
                    $pdf = \App\Http\Controllers\Student\DocsController::genGcvp21();
                } elseif ($oService->name_en == FinanceNomenclature::ENQUIRE_NAME_GCVP6) {
                    $pdf = \App\Http\Controllers\Student\DocsController::genGcvp6();
                } elseif ($oService->name_en == FinanceNomenclature::ENQUIRE_NAME_MILITARY) {
                    $pdf = \App\Http\Controllers\Student\DocsController::genMilitary();
                } elseif ($oService->name_en == FinanceNomenclature::ENQUIRE_NAME_ENTER) {
                    $pdf = \App\Http\Controllers\Student\DocsController::genEntered();
                }

                if ($pdf != false) {
                    $pdf = json_decode($pdf);
                    if( isset($pdf->Transcript) ) {
                        $aReturn['message'] .= '. ' . __('File has been generated successfully you can download it by') . '<a target="_blank" href=' . $pdf['filename'] . '>' . __('next URL') . '</a>';
                    } else {
                        $aReturn['message'] .= '. ' . __("The file was successfully created and printed, you can pick it up within 3 working days at the Registrar's Office. Your document number") .' <strong>' . $pdf->dailyFullId . '</strong>. ';
                    }

                }

                $mailSubject = ($iServiceId == FinanceNomenclature::TRANSIT_CLASS_ATTENDANCE_ID) ? 'Доступ к посещению занятий, для студентов иностранных ВУЗов-партнеров' : 'Была купленна новая услуга';

                // send report on the buy of the service user
                Mail::send('emails.buy_service_user', [
                    'user_id' => $oUser->id,
                    'fio' => $oUser->studentProfile->fio ?? $oUser->name,
                    'service_code' => $oService->code,
                    'service_name' => $oService->name,
                    'cost' => intval($oService->cost),
                    'date' => date('d-m-Y H:i'),
                    'enquireURL' => $pdf->filename ?? ''
                ], function ($message) use ($mailSubject) {
                    $message->from(getcong('site_email'), getcong('site_name'));
                    $message->to(explode(',', env('MAIL_FOR_ERROR_REPORT_FORM_OFFICE')))->subject($mailSubject);
                });

                return \Response::json($aReturn);
            }
        }

        return \Response::json([
            'status' => false,
            'message' => __('Request error')
        ]);


    }


}
