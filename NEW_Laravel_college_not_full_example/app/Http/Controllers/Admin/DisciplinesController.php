<?php

namespace App\Http\Controllers\Admin;

use App\{Discipline,
    DisciplineDocument,
    DisciplinePayCancel,
    EmployeesDepartment,
    StudentDiscipline,
    Models\Discipline\DisciplineSemester,
    Semester,
    Speciality,
    StudyGroupTeacher};
use App\Services\{
    SearchCache,
    SyllabusService
};
use Auth;
use Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Log,
    Response,
    Validator,
};

class DisciplinesController extends MainAdminController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        return view('admin.pages.disciplines');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = Discipline::getDisciplineListForAdmin(
            $request->input('search')['value'],
            $request->input('start', 0),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc',
            \App\Services\Auth::user()->admin_discipline_id_list
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add()
    {
        $specialities = Speciality::get();
        $sectors = EmployeesDepartment::where('is_sector', true)->get();

        $disciplines = [];

        $allDisciplines = Discipline::orderBy('id')->get();

        $discipline = new Discipline();

        $languageLevels = Discipline::$languageLevels;

        $semesters = Semester::getSemestersList();

        return view('admin.pages.addeditDiscipline', compact(
            'specialities',
            'disciplines',
            'allDisciplines',
            'discipline',
            'sectors',
            'languageLevels',
            'semesters'
        ));
    }

    /**
     * Saving
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addedit(Request $request)
    {
        $data = \Input::except(['_token']);

        $inputs = $request->all();

        $defaultsFiledsDiscipline = ['credits'        => Discipline::DISCIPLINE_CREDITS_DEFAULT,
                                     'language_level' => Discipline::DISCIPLINE_LANGUAGE_LVL_DEFAULT];

        $rules = [
            'name' => 'required',
            'practise_1sem_control_start' => ['nullable', 'regex:/\d{2}\.\d{2}/'],
            'practise_1sem_control_end' => ['nullable', 'regex:/\d{2}\.\d{2}/'],
            'practise_2sem_control_start' => ['nullable', 'regex:/\d{2}\.\d{2}/'],
            'practise_2sem_control_end' => ['nullable', 'regex:/\d{2}\.\d{2}/'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        if (!empty($inputs['id'])) {
            $discipline = Discipline::findOrFail($inputs['id']);
        } else {
            $discipline = new Discipline;
        }

        $discipline->fill(array_merge($request->all(), $defaultsFiledsDiscipline)); // так некотрые поля скрыты - добавляем дефаултные значение.

        if ($request->has('is_practice')) {
            $discipline->is_practice = Discipline::IS_PRACTICE_ACTIVE;
        } else {
            $discipline->is_practice = Discipline::IS_PRACTICE_INACTIVE;
        }

        $request->has('has_diplomawork') ? $discipline->has_diplomawork = 1 : $discipline->has_diplomawork = 0;

        if (empty($inputs['ru'])) $discipline->ru = false; else $discipline->ru = true;
        if (empty($inputs['kz'])) $discipline->kz = false; else $discipline->kz = true;
        if (empty($inputs['en'])) $discipline->en = false; else $discipline->en = true;

        $discipline->save();
        SyllabusService::recalculationSyllabusStatus($discipline->id);

        SearchCache::addOrUpdate('admin_disciplines', $discipline->id, [
            'name' => $discipline->name ?? '',
            'ects' => $discipline->ects
        ]);

        if ($request->has('semesters')){
            DisciplineSemester::updateSemesters($discipline->id, $request->semesters);
        }
        if (!empty($inputs['id'])) {
            \Session::flash('flash_message', 'Changes Saved');
        } else {
            \Session::flash('flash_message', 'Added');
        }
        return redirect()->route('adminDisciplineList');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $discipline = Discipline::where('id', $id)->first();

        $sectors = EmployeesDepartment::where('is_sector', true)->get();

        if (!$discipline) {
            abort(404);
        }

        $specialities = Speciality::get();

        $disciplines = [];
        $haveDisciplines = Discipline::select('disciplines.id')
            ->get();

        if (isset($discipline->dependence)) {
            $discipline->dependence = explode(',', $discipline->dependence);
        }

        if (isset($discipline->dependence2)) {
            $discipline->dependence2 = explode(',', $discipline->dependence2);
        }

        if (isset($discipline->dependence3)) {
            $discipline->dependence3 = explode(',', $discipline->dependence3);
        }

        if (isset($discipline->dependence4)) {
            $discipline->dependence4 = explode(',', $discipline->dependence4);
        }

        if (isset($discipline->dependence5)) {
            $discipline->dependence5 = explode(',', $discipline->dependence5);
        }

        foreach ($haveDisciplines as $item) {
            $disciplines[] = $item->id;
        }

        $allDisciplines = Discipline::orderBy('id')->get();

        $languageLevels = Discipline::$languageLevels;

        $semesters = Semester::getSemestersList();

        return view('admin.pages.addeditDiscipline', compact(
            'discipline',
            'specialities',
            'disciplines',
            'allDisciplines',
            'languageLevels',
            'sectors',
            'semesters'
        ));
    }

    public function delete($id)
    {
        $discipline = Discipline::findOrFail($id);

        if (StudentDiscipline::hasStudentsDiscipline($discipline->id)) {
            return redirect()->back()->with('error_message', Lang::getFromJson('Discipline_has_students'));
        }

        \File::delete(public_path() . '/images/uploads/discipline/' . $discipline->image_icon . '-b.jpg');
        \File::delete(public_path() . '/images/uploads/discipline/' . $discipline->image_icon . '-s.jpg');

        $discipline->delete();
        SearchCache::delete('admin_disciplines', $id);

        \Session::flash('flash_message', 'Deleted');

        return redirect()->back();

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxList(Request $request)
    {
        $text = $request->input('text');

        $disciplineList = Discipline
            ::select([
                'id',
                'name',
                'ects'
            ])
            ->orderBy('name')
            ->limit(20);

        if ($text) {
            $disciplineList->whereRaw("name LIKE '" . $text . "%'");
        }

        $disciplineList = $disciplineList->get();

        return Response::json($disciplineList);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPayCancelList(Request $request)
    {
        return view('admin.pages.discipline_pay_cancel.list');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayCancelListAjax(Request $request)
    {
        $searchParams = [];

        if ($request->has('columns')) {
            foreach ($request->input('columns') as $i => $column) {
                if (isset($column['search']['value']) && $column['search']['value'] != '') {
                    $searchParams[$i] = $column['search']['value'];
                }
            }
        }

        $searchData = DisciplinePayCancel::getListForAdmin(
            $request->input('search')['value'],
            $searchParams,
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payCancelSetStatus(Request $request)
    {
        $disciplinePayCancel = DisciplinePayCancel::where('id', $request->input('order_id'))->first();

        if(!$disciplinePayCancel)
        {
            return Response::json(['status' => false]);
        }

        $disciplinePayCancel->status = $request->input('status');
        $disciplinePayCancel->admin_id = \App\Services\Auth::user()->id;
        $disciplinePayCancel->save();
        $disciplinePayCancel->redisCacheRefresh();

        return Response::json(['status' => true]);
    }

    public function groupsJSON(Request $request)
    {
        if (empty($request->input('discipline_id'))) {
            return Response::json([]);
        }

        $groups = StudyGroupTeacher::getGroupsByDiscipline($request->input('discipline_id'));

        return Response::json($groups);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDisciplineSemesters(int $id): \Illuminate\Http\JsonResponse
    {
        $semesters = DisciplineSemester::where('discipline_id', $id)
            ->get();
        $formatSemesters = [];
        foreach ($semesters as $semester){
            $formatSemesters[$semester->semester]['controlForm'][$semester->study_form] = $semester->control_form;
            $formatSemesters[$semester->semester]['hours'][$semester->study_form] = [
                'lecture' => $semester->lecture_hours,
                'practice' => $semester->practical_hours,
                'lab' => $semester->laboratory_hours,
                'sro' => $semester->sro_hours,
                'srop' => $semester->srop_hours,
            ];
        }
        $studentsSemester = [];
        foreach ($formatSemesters as $semester => $hours){
            $hours['name'] = $semester;
            $studentsSemester[] = $hours;
        }
        return response()->json($studentsSemester);
    }

    /**
     * @param $disciplineId
     * @param $semester
     */
    public function deleteDisciplineSemester(int $disciplineId, string $semester): void
    {
        DisciplineSemester::where('semester', $semester)
            ->where('discipline_id', $disciplineId)
            ->delete();
    }
}
