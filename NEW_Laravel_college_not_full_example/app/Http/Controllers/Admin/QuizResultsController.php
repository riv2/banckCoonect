<?php

namespace App\Http\Controllers\Admin;

use App\BcApplications;
use App\Http\Controllers\Controller;
use App\Profiles;
use App\QuizResult;
use App\Speciality;
use App\SpecialityPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class QuizResultsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        $years = Speciality::getUniqueYears();
        $baseEducations = BcApplications::getBaseEducationsArray();
        $studyForms = Profiles::getStudyFormsArray();
        $types = QuizResult::getTypesArray();
        $specialities = Speciality::getArrayForSelect();
        $fullCodes = null;

        return view('admin.quiz_results.list', compact('fullCodes', 'years', 'studyForms', 'baseEducations', 'types', 'specialities'));
    }

    /**
     * Ajax answer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = QuizResult::getListForAdmin(
            $request->input('search')['value'],
            $request->input('columns')[2]['search']['value'],
            $request->input('columns')[3]['search']['value'],
            $request->input('columns')[4]['search']['value'],
            $request->input('columns')[5]['search']['value'],
            $request->input('columns')[7]['search']['value'],
            $request->input('start', 0),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);
    }

    public function view(int $id, Request $request)
    {
        $quizResult = QuizResult::where('id', $id)->first();

        if (empty($quizResult)) {
            $this->flash_danger('Результат не найден');
            return redirect()->route('adminQuizResults');
        }

        return view('admin.quiz_results.view', compact('quizResult'));
    }
}