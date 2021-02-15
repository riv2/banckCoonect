<?php
/**
 * User: dadicc
 * Date: 3/1/20
 * Time: 8:48 PM
 */

namespace App\Http\Controllers\Admin;

use App\{
    CheckList,
    CheckListExam,
    EntranceExam,
    EntranceExamFiles,
    EntranceExamUser,
    Speciality
};
use App\Validators\{
    AdminCheckListGetEntranceExamListValidator,
    AdminCheckListEditPostValidator,
    AdminCheckListRemoveValidator
};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Log,Response,View};

class CheckListController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list( Request $request )
    {
        $aYearsList = EntranceExam::generateYearsForAdmin();
        return view('admin.pages.check_list.list',[
            'yearsList' => $aYearsList
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList( Request $request )
    {

        $iPage = ( $request->has('page') && (intval($request->input('page')) > 0) ) ? intval($request->input('page')) : 1;
        $sYear = ( $request->has('year') ) ? $request->input('year') : date('Y');

        $oCheckList = CheckList::
        where('year',$sYear)->
        whereNull('deleted_at')->
        paginate(10, ['*'], 'page', $iPage);

        if( !empty($oCheckList) )
        {
            $oCheckList->getCollection()->transform(function (&$value) {

                if( !empty($value->speciality) ){
                    $value->speciality_name = $value->speciality->name;
                }
                if( !empty($value->basic_education) ){
                    $value->basic_education_name = __($value->basic_education);
                }
                if( !empty($value->citizenship) ){
                    $value->citizenship_name = __($value->citizenship);
                }
                if( !empty($value->education_level) ){
                    $value->education_level_name = __($value->education_level);
                }

                return $value;
            });
        }

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'models'     => $oCheckList
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit( Request $request )
    {

        $entranceExam = [];
        if( $request->has('id') && ( intval($request->input('id')) > 0 ) ){

            $oCheckList = CheckList::
            where('id',intval($request->input('id')))->
            whereNull('deleted_at')->
            first();

            if( empty($oCheckList) ){ abort(404); }

            // достаем все ВИ через связь с ПЛ
            $oCheckListExam = CheckListExam::
            where('check_list_id',intval($request->input('id')))->
            whereNull('deleted_at')->
            get();
            if( !empty($oCheckListExam) && (count($oCheckListExam) > 0) )
            {
                foreach( $oCheckListExam as $itemCLE )
                {
                    $oCur = EntranceExam::getById( $itemCLE->entrance_exam_id );
                    if( !empty($oCur) ){ $entranceExam[] = $oCur; }
                }
            }

        } else {

            $oCheckList = new CheckList();
        }

        return view('admin.pages.check_list.edit',[
            'model'            => $oCheckList,
            'yearsList'        => EntranceExam::generateYearsForAdmin(),
            'specialityList'   => CheckList::getSpecialityList(),
            'basicEducation'   => CheckList::getBasicEducation(),
            'citizenshipList'  => CheckList::getCitizenshipList(),
            'educationLevel'   => CheckList::getEducationLevel(),
            'entranceExamList' => $entranceExam,
            'loadEE'           => ( !empty($entranceExam) && (count($entranceExam) > 0) ) ? true : false,
            'isEdit'           => intval($request->input('id')) ?? 0
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editPost( Request $request )
    {

        //Log::info( 'data: ' . var_export($request->all(),true) );

        // validation data
        $obValidator = AdminCheckListEditPostValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            \Session::flash('flash_message', __('Data not saved'));
            return redirect()->back();
        }

        if( $request->has('isEdit') && ( intval($request->input('isEdit')) > 0) )
        {
            $oCheckList = CheckList::
            where('id',intval($request->input('isEdit')))->
            whereNull('deleted_at')->
            first();

        } else {

            $oCheckList = new CheckList();
        }

        $oCheckList->fill( $request->all() );
        $oCheckList->syncFields( $request->all() );
        $oCheckList->save();

        // sync entrance_exam data
        $oCheckList->syncEntranceExamData( $request->all(), $oCheckList->id );

        \Session::flash('flash_message', __('Success'));
        return redirect()->route( 'adminCheckListEdit',['id' => $oCheckList->id] );

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEntranceExamList( Request $request )
    {

        // validation data
        $obValidator = AdminCheckListGetEntranceExamListValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return Response::json([
                'status'   => false,
                'message'  => __('Data not saved')
            ]);
        }

        $sYear = ( $request->has('year') && !empty($request->input('year')) ) ? $request->input('year') : date('Y');

        $oEntranceExam = EntranceExam::
        where('year',$sYear)->
        whereNull('deleted_at')->
        get();

        return Response::json([
            'status' => true,
            'models' => $oEntranceExam
        ]);

    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function renderEntranceExamItem( Request $request )
    {

        if( $request->has('id') && ( intval($request->input('id')) > 0 ) )
        {

            $oEntranceExam = EntranceExam::
            where('id',intval($request->input('id')))->
            whereNull('deleted_at')->
            first();

        } else {

            $oEntranceExam = new EntranceExam();
        }

        return View::make('admin.pages.check_list.entranceExamItem',[
            'model'      => $oEntranceExam,
            'yearsList'  => EntranceExam::generateYearsForAdmin()
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove( Request $request )
    {

        // validation data
        $obValidator = AdminCheckListRemoveValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return Response::json([
                'status'   => false,
                'message'  => __('Data not find')
            ]);
        }

        CheckList::removeById( $request->input('id') );

        return Response::json([
            'status'  => true,
            'message' => __('Success')
        ]);

    }


}