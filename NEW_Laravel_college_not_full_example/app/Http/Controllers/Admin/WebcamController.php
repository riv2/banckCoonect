<?php

namespace App\Http\Controllers\Admin;

use App\Profiles;
use App\Services\Auth;
use App\Webcam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use File;

class WebcamController extends Controller
{
    public function index()
    {
        $testTypes = Webcam::TEST_TYPES;
        $educationsStudyForms = Profiles::$studyForms;

        return view('admin.pages.webcam.index', compact('testTypes', 'educationsStudyForms'));
    }

    public function getListAjax(Request $request)
    {

        return datatables()->eloquent(Webcam::with('discipline'))
            ->filterColumn('discipline', function ($query,  $key){
                $query->whereHas('discipline', function ($q) use ($key){
                    $q->where('name', 'LIKE', '%'.$key.'%');
                });
            })
            ->filterColumn('user', function ($query,  $key){
                $query->whereHas('user', function ($q) use ($key){
                    return $q->whereHas('studentProfile', function($q2) use ($key){
                        return $q2->where('fio', 'like', '%'.$key.'%');
                    });
                });
            })
            ->filterColumn('study_form', function ($query,  $key){
                $query->whereHas('user', function ($q) use ($key){
                    return $q->whereHas('studentProfile', function($q2) use ($key){
                        return $q2->where('education_study_form', 'like', '%'.$key.'%');
                    });
                });
            })
            ->addColumn('study_form', function (Webcam $record){
                $studyForm = $record->user->studentProfile->education_study_form;
                return __($studyForm ?? '');
            })
            ->addColumn('discipline', function (Webcam $record){
                return $record->discipline->name;
            })
            ->addColumn('type', function (Webcam $record){
                if (isset($record->type) and $record->type !== ''){
                    return Webcam::TEST_TYPES[$record->type];
                }
                return '';
            })
            ->addColumn('user', function (Webcam $record){
                return $record->user->studentProfile->fio;
            })
            ->addColumn('actions', function (Webcam $record){
                $actions = '<a class="btn btn-default" target="_blank" href="/webcamfiles/'.$record->file_name.'">
                               <i class="fa fa-film"></i>
                            </a>';
                if (Auth::user()->hasRight('webcam', 'delete')){
                    $actions .=  '<a class="btn btn-default-dark" style="margin-left:5px" href="'.route('admin.webcam.delete', ['id' => $record->id]).'">
                                      <i class="fa fa-trash"></i>
                                  </a>';
                }
                return $actions;
            })
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->removeColumn('user_id')
            ->removeColumn('discipline_id')
            ->removeColumn('file_name')
            ->removeColumn('updated')
            ->toJson();
    }

    public function deleteWebcamRecord($id)
    {
        $webcam = Webcam::find($id);

        if (isset($webcam)){
            $webcam->delete();
            File::delete(public_path('webcamfiles/'.$webcam->file_name));
        }
        return redirect()->back()->with(['flash_success' => "Запись успешно удалена!"]);
    }
}
