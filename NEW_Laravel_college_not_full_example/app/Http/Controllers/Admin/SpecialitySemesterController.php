<?php

namespace App\Http\Controllers\Admin;

use App\BcApplications;
use App\Http\Controllers\Controller;
use App\Profiles;
use App\Semester;
use App\Speciality;
use App\SpecialitySemester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SpecialitySemesterController extends Controller
{
    public function index()
    {
        $baseEducations = BcApplications::getBaseEducationsArray();
        $studyForms = Profiles::getStudyFormsArray();
        $specialities = Speciality::getArrayForSelect();
        $types = Semester::getTypesForSpecialitySemestersSelect();

        return view('admin.speciality_semesters.index', compact('baseEducations', 'studyForms', 'specialities', 'types'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = SpecialitySemester::getListForAdmin(
            $request->input('columns')[0]['search']['value'],
            $request->input('columns')[1]['search']['value'],
            $request->input('columns')[2]['search']['value'],
            $request->input('columns')[3]['search']['value'],
            $request->input('columns')[4]['search']['value'],
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

    public function save(Request $request)
    {
        if (
            empty($request->input('speciality_ids')) ||
            empty($request->input('study_form')) ||
            empty($request->input('base_education')) ||
            empty($request->input('type')) ||
            empty($request->input('semester')) ||
            empty($request->input('from')) ||
            empty($request->input('to'))
        ) {
            return Response::json(['error' => 'Необходимо заполнить все поля']);
        }

        if (\DateTimeImmutable::createFromFormat('Y-m-d', $request->input('from')) > \DateTimeImmutable::createFromFormat('Y-m-d', $request->input('to'))) {
            return Response::json(['error' => 'Проверьте даты От и До']);
        }

        // Base Educations
        if ($request->input('base_education') == 'all') {
            $baseEducations = BcApplications::getBaseEducationsArrayFlat();
        } else {
            $baseEducations = (array)$request->input('base_education');
        }

        // Study Forms
        if ($request->input('study_form') == 'all') {
            $studyForms = Profiles::getStudyFormsArrayFlat();
        } else {
            $studyForms = (array)$request->input('study_form');
        }

        foreach ($request->input('speciality_ids') as $specialityId) {
            foreach ($baseEducations as $baseEducation) {
                foreach ($studyForms as $studyForm) {
                    $SS = SpecialitySemester::getOne(
                        $specialityId,
                        $studyForm,
                        $baseEducation,
                        $request->input('semester'),
                        $request->input('type')
                    );

                    if (empty($SS)) {
                        $SS = new SpecialitySemester;
                        $SS->speciality_id = $specialityId;
                        $SS->study_form = $studyForm;
                        $SS->base_education = $baseEducation;
                        $SS->type = $request->input('type');
                        $SS->semester = $request->input('semester');
                    }

                    $SS->start_date = $request->input('from');
                    $SS->end_date = $request->input('to');

                    if (!$SS->save()) {
                        return Response::json(['error' => 'Ошибка при сохранении']);
                    }
                }
            }
        }

        return Response::json(['success' => true]);
    }

    public function edit(Request $request)
    {
        if (
            empty($request->input('id')) ||
            empty($request->input('from')) ||
            empty($request->input('to'))
        ) {
            return Response::json(['error' => 'Ошибка. Необходимо заполнить все поля']);
        }

        if (\DateTimeImmutable::createFromFormat('Y-m-d', $request->input('from')) > \DateTimeImmutable::createFromFormat('Y-m-d', $request->input('to'))) {
            return Response::json(['error' => 'Ошибка. Проверьте даты От и До']);
        }

        $SS = SpecialitySemester::where('id', $request->input('id'))->first();

        if (empty($SS)) {
            return Response::json(['error' => 'Ошибка. Срок с id=' . $request->input('id') . ' не найден']);
        }

        $SS->start_date = $request->input('from');
        $SS->end_date = $request->input('to');

        if (!$SS->save()) {
            return Response::json(['error' => 'Ошибка при сохранении']);
        }

        return Response::json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDefaultListAjax(Request $request)
    {
        $searchData = Semester::getListForAdmin(
            $request->input('columns')[0]['search']['value'],
            $request->input('columns')[1]['search']['value'],
            $request->input('columns')[2]['search']['value'],
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

    public function editDefault(Request $request)
    {
        if (
            empty($request->input('id')) ||
            empty($request->input('from')) ||
            empty($request->input('to'))
        ) {
            return Response::json(['error' => 'Ошибка. Необходимо заполнить все поля']);
        }

        if (\DateTimeImmutable::createFromFormat('Y-m-d', $request->input('from')) > \DateTimeImmutable::createFromFormat('Y-m-d', $request->input('to'))) {
            return Response::json(['error' => 'Ошибка. Проверьте даты От и До']);
        }

        $semester = Semester::where('id', $request->input('id'))->first();

        if (empty($semester)) {
            return Response::json(['error' => 'Ошибка. Срок с id=' . $request->input('id') . ' не найден']);
        }

        $semester->start_date = $request->input('from');
        $semester->end_date = $request->input('to');

        if (!$semester->save()) {
            return Response::json(['error' => 'Ошибка при сохранении']);
        }

        return Response::json(['success' => true]);
    }
}