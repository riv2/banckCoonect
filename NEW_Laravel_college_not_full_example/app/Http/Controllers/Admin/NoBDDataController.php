<?php
/**
 * User: dadicc
 * Date: 4/8/20
 * Time: 11:04 AM
 */

namespace App\Http\Controllers\Admin;

use App\Models\{
    NobdAcademicLeave,
    NobdAcademicMobility,
    NobdCauseStayYear,
    NobdCountry,
    NobdDisabilityGroup,
    NobdEmploymentOpportunity,
    NobdEvents,
    NobdExchangeSpecialty,
    NobdFormDiplom,
    NobdLanguage,
    NobdPaymentType,
    NobdReasonDisposal,
    NobdReward,
    NobdTrainedQuota,
    NobdTypeDirection,
    NobdTypeEvent,
    NobdTypeViolation,
    NobdStudyExchange,
    NobdUser
};
use App\Http\Controllers\Controller;
use App\Validators\{
    AdminNoBDDataControllerEditItemValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log,Response,View};


class NoBDDataController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list( Request $request )
    {

        return view('admin.pages.nobddata.index');
    }


    /**
     * @param Request $request
     */
    public function getNobdAcademicLeave( Request $request )
    {

        $page = ( $request->has('nobd_academic_leave') && ($request->input('nobd_academic_leave') != '') ) ? $request->input('nobd_academic_leave') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdAcademicLeave::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdAcademicMobility( Request $request )
    {

        $page = ( $request->has('nobd_academic_mobility') && ($request->input('nobd_academic_mobility') != '') ) ? $request->input('nobd_academic_mobility') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdAcademicMobility::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdCauseStayYear( Request $request )
    {

        $page = ( $request->has('nobd_cause_stay_year') && ($request->input('nobd_cause_stay_year') != '') ) ? $request->input('nobd_cause_stay_year') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdCauseStayYear::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdCountry( Request $request )
    {

        $page = ( $request->has('nobd_country') && ($request->input('nobd_country') != '') ) ? $request->input('nobd_country') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdCountry::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdDisabilityGroup( Request $request )
    {

        $page = ( $request->has('nobd_disability_group') && ($request->input('nobd_disability_group') != '') ) ? $request->input('nobd_disability_group') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdDisabilityGroup::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdEmploymentOpportunity( Request $request )
    {

        $page = ( $request->has('nobd_employment_opportunity') && ($request->input('nobd_employment_opportunity') != '') ) ? $request->input('nobd_employment_opportunity') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdEmploymentOpportunity::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdEvents( Request $request )
    {

        $page = ( $request->has('nobd_events') && ($request->input('nobd_events') != '') ) ? $request->input('nobd_events') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdEvents::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdExchangeSpecialty( Request $request )
    {

        $page = ( $request->has('nobd_exchange_specialty') && ($request->input('nobd_exchange_specialty') != '') ) ? $request->input('nobd_exchange_specialty') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdExchangeSpecialty::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdFormDiplom( Request $request )
    {

        $page = ( $request->has('nobd_form_diplom') && ($request->input('nobd_form_diplom') != '') ) ? $request->input('nobd_form_diplom') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdFormDiplom::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdLanguage( Request $request )
    {

        $page = ( $request->has('nobd_language') && ($request->input('nobd_language') != '') ) ? $request->input('nobd_language') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdLanguage::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdPaymentType( Request $request )
    {

        $page = ( $request->has('nobd_payment_type') && ($request->input('nobd_payment_type') != '') ) ? $request->input('nobd_payment_type') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdPaymentType::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdReasonDisposal( Request $request )
    {

        $page = ( $request->has('nobd_reason_disposal') && ($request->input('nobd_reason_disposal') != '') ) ? $request->input('nobd_reason_disposal') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdReasonDisposal::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdReward( Request $request )
    {

        $page = ( $request->has('nobd_reward') && ($request->input('nobd_reward') != '') ) ? $request->input('nobd_reward') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdReward::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdTrainedQuota( Request $request )
    {

        $page = ( $request->has('nobd_trained_quota') && ($request->input('nobd_trained_quota') != '') ) ? $request->input('nobd_trained_quota') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdTrainedQuota::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdTypeDirection( Request $request )
    {

        $page = ( $request->has('nobd_type_direction') && ($request->input('nobd_type_direction') != '') ) ? $request->input('nobd_type_direction') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdTypeDirection::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdTypeEvent( Request $request )
    {

        $page = ( $request->has('nobd_type_event') && ($request->input('nobd_type_event') != '') ) ? $request->input('nobd_type_event') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdTypeEvent::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdTypeViolation( Request $request )
    {

        $page = ( $request->has('nobd_type_violation') && ($request->input('nobd_type_violation') != '') ) ? $request->input('nobd_type_violation') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdTypeViolation::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNobdStudyExchange( Request $request )
    {

        $page = ( $request->has('nobd_type_violation') && ($request->input('nobd_type_violation') != '') ) ? $request->input('nobd_type_violation') : null;

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => NobdStudyExchange::getList( $page )
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItem( Request $request )
    {

        if( !$request->has('type') || !$request->has('id') )
        {
            return Response::json([
                'status'     => false,
                'message'    => __('Data not found')
            ]);
        }

        $iId    = $request->input('id');
        $sType  = $request->input('type');
        $oModel = null;

        switch( $sType )
        {
            case 'nobd_academic_leave':
                $oModel = ($iId != 0) ? NobdAcademicLeave::getItem( $iId ) : NobdAcademicLeave::createOrFind( $iId );
            break;
            case 'nobd_academic_mobility':
                $oModel = ($iId != 0) ? NobdAcademicMobility::getItem( $iId ) : NobdAcademicMobility::createOrFind( $iId );
            break;
            case 'nobd_cause_stay_year':
                $oModel = ($iId != 0) ? NobdCauseStayYear::getItem( $iId ) : NobdCauseStayYear::createOrFind( $iId );
            break;
            case 'nobd_country':
                $oModel = ($iId != 0) ? NobdCountry::getItem( $iId ) : NobdCountry::createOrFind( $iId );
            break;
            case 'nobd_disability_group':
                $oModel = ($iId != 0) ? NobdDisabilityGroup::getItem( $iId ) : NobdDisabilityGroup::createOrFind( $iId );
            break;
            case 'nobd_employment_opportunity':
                $oModel = ($iId != 0) ? NobdEmploymentOpportunity::getItem( $iId ) : NobdEmploymentOpportunity::createOrFind( $iId );
            break;
            case 'nobd_events':
                $oModel = ($iId != 0) ? NobdEvents::getItem( $iId ) : NobdEvents::createOrFind( $iId );
            break;
            case 'nobd_exchange_specialty':
                $oModel = ($iId != 0) ? NobdExchangeSpecialty::getItem( $iId ) : NobdExchangeSpecialty::createOrFind( $iId );
            break;
            case 'nobd_form_diplom':
                $oModel = ($iId != 0) ? NobdFormDiplom::getItem( $iId ) : NobdFormDiplom::createOrFind( $iId );
            break;
            case 'nobd_language':
                $oModel = ($iId != 0) ? NobdLanguage::getItem( $iId ) : NobdLanguage::createOrFind( $iId );
            break;
            case 'nobd_payment_type':
                $oModel = ($iId != 0) ? NobdPaymentType::getItem( $iId ) : NobdPaymentType::createOrFind( $iId );
            break;
            case 'nobd_reason_disposal':
                $oModel = ($iId != 0) ? NobdReasonDisposal::getItem( $iId ) : NobdReasonDisposal::createOrFind( $iId );
            break;
            case 'nobd_reward':
                $oModel = ($iId != 0) ? NobdReward::getItem( $iId ) : NobdReward::createOrFind( $iId );
            break;
            case 'nobd_trained_quota':
                $oModel = ($iId != 0) ? NobdTrainedQuota::getItem( $iId ) : NobdTrainedQuota::createOrFind( $iId );
            break;
            case 'nobd_type_direction':
                $oModel = ($iId != 0) ? NobdTypeDirection::getItem( $iId ) : NobdTypeDirection::createOrFind( $iId );
            break;
            case 'nobd_type_event':
                $oModel = ($iId != 0) ? NobdTypeEvent::getItem( $iId ) : NobdTypeEvent::createOrFind( $iId );
            break;
            case 'nobd_type_violation':
                $oModel = ($iId != 0) ? NobdTypeViolation::getItem( $iId ) : NobdTypeViolation::createOrFind( $iId );
            break;
            case 'nobd_study_exchange':
                $oModel = ($iId != 0) ? NobdStudyExchange::getItem( $iId ) : NobdStudyExchange::createOrFind( $iId );
            break;

        }

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => $oModel
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItem( Request $request )
    {

        if( !$request->has('type') || !$request->has('id') )
        {
            return Response::json([
                'status'     => false,
                'message'    => __('Data not found')
            ]);
        }

        $iId    = $request->input('id');
        $sType  = $request->input('type');

        switch( $sType )
        {
            case 'nobd_academic_leave':
                $oModel = NobdAcademicLeave::remove( $iId );
            break;
            case 'nobd_academic_mobility':
                $oModel = NobdAcademicMobility::remove( $iId );
            break;
            case 'nobd_cause_stay_year':
                $oModel = NobdCauseStayYear::remove( $iId );
            break;
            case 'nobd_country':
                $oModel = NobdCountry::remove( $iId );
            break;
            case 'nobd_disability_group':
                $oModel = NobdDisabilityGroup::remove( $iId );
            break;
            case 'nobd_employment_opportunity':
                $oModel = NobdEmploymentOpportunity::remove( $iId );
            break;
            case 'nobd_events':
                $oModel = NobdEvents::remove( $iId );
            break;
            case 'nobd_exchange_specialty':
                $oModel = NobdExchangeSpecialty::remove( $iId );
            break;
            case 'nobd_form_diplom':
                $oModel = NobdFormDiplom::remove( $iId );
            break;
            case 'nobd_language':
                $oModel = NobdLanguage::remove( $iId );
            break;
            case 'nobd_payment_type':
                $oModel = NobdPaymentType::remove( $iId );
            break;
            case 'nobd_reason_disposal':
                $oModel = NobdReasonDisposal::remove( $iId );
            break;
            case 'nobd_reward':
                $oModel = NobdReward::remove( $iId );
            break;
            case 'nobd_trained_quota':
                $oModel = NobdTrainedQuota::remove( $iId );
            break;
            case 'nobd_type_direction':
                $oModel = NobdTypeDirection::remove( $iId );
            break;
            case 'nobd_type_event':
                $oModel = NobdTypeEvent::remove( $iId );
            break;
            case 'nobd_type_violation':
                $oModel = NobdTypeViolation::remove( $iId );
            break;
            case 'nobd_study_exchange':
                $oModel = NobdStudyExchange::remove( $iId );
            break;
        }

        return Response::json([
            'status'     => true,
            'message'    => __('Success')
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editItem( Request $request )
    {

        // validation data
        $obValidator = AdminNoBDDataControllerEditItemValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return Response::json([
                'status'     => false,
                'message'    => __('Data not found')
            ]);
        }

        $iId    = $request->input('id');
        $sType  = $request->input('type');
        $oModel = null;

        switch( $sType )
        {
            case 'nobd_academic_leave':
                $oModel = NobdAcademicLeave::createOrFind( $iId );
            break;
            case 'nobd_academic_mobility':
                $oModel = NobdAcademicMobility::createOrFind( $iId );
            break;
            case 'nobd_cause_stay_year':
                $oModel = NobdCauseStayYear::createOrFind( $iId );
            break;
            case 'nobd_country':
                $oModel = NobdCountry::createOrFind( $iId );
            break;
            case 'nobd_disability_group':
                $oModel = NobdDisabilityGroup::createOrFind( $iId );
            break;
            case 'nobd_employment_opportunity':
                $oModel = NobdEmploymentOpportunity::createOrFind( $iId );
            break;
            case 'nobd_events':
                $oModel = NobdEvents::createOrFind( $iId );
            break;
            case 'nobd_exchange_specialty':
                $oModel = NobdExchangeSpecialty::createOrFind( $iId );
            break;
            case 'nobd_form_diplom':
                $oModel = NobdFormDiplom::createOrFind( $iId );
            break;
            case 'nobd_language':
                $oModel = NobdLanguage::createOrFind( $iId );
            break;
            case 'nobd_payment_type':
                $oModel = NobdPaymentType::createOrFind( $iId );
            break;
            case 'nobd_reason_disposal':
                $oModel = NobdReasonDisposal::createOrFind( $iId );
            break;
            case 'nobd_reward':
                $oModel = NobdReward::createOrFind( $iId );
            break;
            case 'nobd_trained_quota':
                $oModel = NobdTrainedQuota::createOrFind( $iId );
            break;
            case 'nobd_type_direction':
                $oModel = NobdTypeDirection::createOrFind( $iId );
            break;
            case 'nobd_type_event':
                $oModel = NobdTypeEvent::createOrFind( $iId );
            break;
            case 'nobd_type_violation':
                $oModel = NobdTypeViolation::createOrFind( $iId );
            break;
            case 'nobd_study_exchange':
                $oModel = NobdStudyExchange::createOrFind( $iId );
            break;
        }

        $oModel->fill($request->input('model'));
        $oModel->save();

        return Response::json([
            'status'     => true,
            'message'    => __('Success')
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataByUserId( Request $request )
    {

        if( !$request->has('user') || empty($request->input('user') ) )
        {
            return Response::json([
                'status'     => false,
                'message'    => __('Data not found')
            ]);
        }


        $oNobdUser = NobdUser::
        with('studyExchangeRef')->
        with('pc')->
        where('user_id',$request->input('user'))->
        whereNull('deleted_at')->
        first();

        if( empty($oNobdUser) )
        {
            $oNobdUser = new NobdUser();
            $oNobdUser->fill([
                "user_id" => '',
                "study_exchange" => '',
                "host_country" => '',
                "host_university_name" => '',
                "host_university_language" => '',
                "exchange_specialty" => '',
                "exchange_specialty_st" => '',
                "exchange_date_start" => null,
                "exchange_date_end" => null,
                "academic_mobility" => '',
                "academic_leave" => '',
                "academic_leave_order_number" => '',
                "academic_leave_order_date" => null,
                "academic_leave_out_order_number" => '',
                "academic_leave_out_order_date" => null,
                "is_national_student_league" => '',
                "is_world_winter_universiade" => '',
                "is_world_summer_universiade" => '',
                "is_winter_universiade_republic_kz" => '',
                "is_summer_universiade_republic_kz" => '',
                "is_nonresident_student" => '',
                "is_needs_hostel" => '',
                "is_lives_hostel" => '',
                "payment_type" => '',
                "cost_education" => '',
                "number_grant_certificate" => '',
                "trained_quota" => '',
                "cause_stay_year" => '',
                "is_participation_competitions" => '',
                "is_orphan" => '',
                "is_child_without_parents" => '',
                "is_invalid" => '',
                "disability_group" => '',
                "type_violation" => '',
                "conclusion_pmpc" => '',
                "conclusion_date" => null,
                "is_thesis_defense" => '',
                "form_diplom" => '',
                "diplom_series" => '',
                "diplom_number" => '',
                "date_disposal" => null,
                "number_disposal_order" => '',
                "reason_disposal" => '',
                "employment_opportunity"  => ''
            ]);
        }

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'model'      => $oNobdUser
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function renderNobdUserPc( Request $request )
    {

        return View::make('admin.pages.nobddata.renderUserPcItem',[
            'typeEvent'       => NobdTypeEvent::whereNull('deleted_at')->get(),
            'typeDirection'   => NobdTypeDirection::whereNull('deleted_at')->get(),
            'events'          => NobdEvents::whereNull('deleted_at')->get(),
            'reward'          => NobdReward::whereNull('deleted_at')->get(),
            'count'           => $request->input('count',0)
        ]);
    }






}








