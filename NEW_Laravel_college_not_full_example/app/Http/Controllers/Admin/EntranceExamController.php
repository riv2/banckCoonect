<?php
/**
 * User: dadicc
 * Date: 3/1/20
 * Time: 8:21 PM
 */

namespace App\Http\Controllers\Admin;

use App\{
    CheckListExam,
    EntranceExam,
    EntranceExamFiles,
    EntranceExamUser
};
use App\Http\Controllers\Controller;
use App\Validators\{
    AdminEntranceExamEditPostValidator,
    AdminEntranceExamRemoveValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Log,Response};

class EntranceExamController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list( Request $request )
    {
        $aYearsList = EntranceExam::generateYearsForAdmin();
        return view('admin.pages.entrance_exam.list',[
            'yearsList' => $aYearsList
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList( Request $request )
    {

        $iPage = ( $request->has('page') && ( intval($request->input('page')) > 0) ) ? intval($request->input('page')) : 1;
        $sYear = ( $request->has('year') ) ? $request->input('year') : date('Y');

        $oEntranceExam = EntranceExam::
        where('year',$sYear)->
        whereNull('deleted_at')->
        paginate(10, ['*'], 'page',$iPage);

        return Response::json([
            'status'     => true,
            'message'    => __('Success'),
            'models'     => $oEntranceExam
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit( Request $request )
    {

        if( $request->has('id') && ( intval($request->input('id')) > 0) )
        {

            $oEntranceExam = EntranceExam::
            where('id',intval($request->input('id')))->
            whereNull('deleted_at')->
            first();

            if( empty($oEntranceExam) ){ abort(404); }

        } else {

            $oEntranceExam = new EntranceExam();
        }

        return view('admin.pages.entrance_exam.edit',[
            'model'     => $oEntranceExam,
            'yearsList' => EntranceExam::generateYearsForAdmin(),
            'isEdit'    => intval($request->input('id')) ?? 0

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
        $obValidator = AdminEntranceExamEditPostValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            \Session::flash('flash_message', __('Data not saved'));
            return redirect()->back();
        }

        if( $request->has('isEdit') && ( intval($request->input('isEdit')) > 0) )
        {
            $oEntranceExam = EntranceExam::
            where('id',intval($request->input('isEdit')))->
            whereNull('deleted_at')->
            first();

        } else {

            $oEntranceExam = new EntranceExam();
        }

        $oEntranceExam->fill( $request->all() );
        $oEntranceExam->syncFields( $request->all() );
        $oEntranceExam->save();
        $oEntranceExam->saveFiles( $request->all() );

        \Session::flash('flash_message', __('Success'));
        return redirect()->route( 'adminEntranceExamEdit',['id' => $oEntranceExam->id] );

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove( Request $request )
    {

        // validation data
        $obValidator = AdminEntranceExamRemoveValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return Response::json([
                'status'  => false,
                'message' => __('Data not saved')
            ]);
        }

        $oEntranceExam = EntranceExam::
        where('id',$request->input('id'))->
        whereNull('deleted_at')->
        first();

        if( !empty($oEntranceExam) )
        {

            // удаляем связь с ПЛ
            $oCheckListExam = CheckListExam::
            where('entrance_exam_id',$oEntranceExam->id)->
            whereNull('deleted_at')->
            delete();

            // удаляем (безопасно) результаты студиком по ВИ
            $oEntranceExamUser = EntranceExamUser::
            where('entrance_exam_id',$oEntranceExam->id)->
            whereNull('deleted_at')->
            delete();

            // удаляем привязанные файлы
            $EntranceExamFilesIds = [];
            $oEntranceExamFiles = EntranceExamFiles::
            where('entrance_exam_id',$oEntranceExam->id)->
            whereNull('deleted_at')->
            get();
            if( !empty($oEntranceExamFiles) )
            {
                foreach($oEntranceExamFiles as $entranceExamFilesItem)
                {
                    $EntranceExamFilesIds[] = $entranceExamFilesItem->id;
                }
            }
            EntranceExamFiles::removeFiles( $EntranceExamFilesIds );

            $oEntranceExam->delete();
        }

        return Response::json([
            'status'  => true,
            'message' => __('Success')
        ]);

    }



}