<?php
namespace App\Http\Controllers\Admin;

use App\Discipline;
use App\EntranceTest;
use App\Http\Requests\Specialities\AddDeleteSpecialityDisciplineSemesterRequest;
use App\Http\Requests\Speciality\AddDependenceRequest;
use App\Models\Discipline\DisciplineSemester;
use App\Models\Speciality\SpecialityDisciplineDependence;
use App\Models\Speciality\SpecialityDisciplineDependenceDiscipline;
use App\Models\Speciality\SpecialityDisciplineSemester;
use App\Module;
use App\Semester;
use App\SpecialityDiscipline;
use App\SpecialityPrice;
use App\Subject;
use App\Trend;
use Auth;
use App\Speciality;
use Carbon\Carbon;
use App\Http\Requests;
use App\Jobs\{
    UpdateStudentSubmodules,
    UpdateStudentDisciplines
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{App,DB,Image,Log,Response,Validator};
use Illuminate\Validation\Rule;
use Session;
use App\Validators\{AdminAjaxGetListForSpecialityEditValidator};
use Yajra\Datatables\Datatables;

class SpecialitiesController extends MainAdminController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
	
	phpinfo();
	
	die();
	
	
        $fullCodes = Speciality::getUniqueFullCodes();
        $years = Speciality::getUniqueYears();

        return view('admin.pages.specialities', compact('fullCodes', 'years'));
    }

    /**
     * Ajax answer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = Speciality::getListForAdmin(
            $request->input('search')['value'],
            $request->input('columns')[1]['search']['value'],
            $request->input('columns')[2]['search']['value'],
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

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add()
    {
        $subjectList = Subject::get();
        $trendList = Trend::get();
        $speciality = new Speciality();
        $disciplineList = Discipline::get();
        $entranceTestList = EntranceTest::get();
        $moduleList = Module
            ::with('disciplines')
            ->whereHas('disciplines')
            ->get();
        $disciplineCycles = Discipline::$cycles;
        $mtTks = Discipline::$mtTks;
        $languageTypes = Discipline::$languageTypes;
        $semesters = Semester::getSemestersList();
        $specialityDisciplines = [];

        return view('admin.pages.addeditSpeciality', [
            'subjectList' => $subjectList,
            'trendList' => $trendList,
            'speciality' => $speciality,
            'disciplineList' => $disciplineList,
            'entranceTestList' => $entranceTestList,
            'moduleList' => $moduleList,
            'disciplineCycles' => $disciplineCycles,
            'mtTks' => $mtTks,
            'languageTypes' => $languageTypes,
            'semesters' => $semesters,
            'specialityDisciplines' => $specialityDisciplines
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addedit(Request $request)
    {   

        $data = \Input::except(['_token']);

        $code = explode(':', $data['code_char']);

        $data = array_merge($data, [
            'code_char'     => $code[0],
            'code_number'   => $code[1],
        ]);

        $inputs = $request->merge([
            'code_char'     => $code[0],
            'code_number'   => $code[1],
        ]);

        $rule = [
            'name' => 'required',
            'code_char' => ['required', Rule::in(['b', 'm'])],
            'url' => 'required|max:256|unique:specialities,id'
        ];
        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        if (!empty($inputs['id'])) {
            $speciality = Speciality::findOrFail($inputs['id']);
            $isNew = false;
        } else {
            $speciality = new Speciality;
            $isNew = true;
        }
        
        
        $requestAll = $request->all();
        
        $speciality->fill($requestAll);
        $speciality->check_entrance_test = $inputs['entrance_test'] ?? false;
        $speciality->save();

        if (!empty($inputs['entrance_test']) && ($inputs['entrance_test'] == true) && $request->input('entrance_test_id')) {
            $speciality->entranceTests()->sync($inputs['entrance_test_id']);
        } else {
            $speciality->entranceTests()->sync([]);
        }

        $speciality->attachSubjects($this->specialitySubjectDecorator($request->input('subject')));
        $speciality->submodules()->sync($this->specialitySubmoduleDecorator($request->input('submodule')));

        UpdateStudentDisciplines::dispatch($speciality);
        UpdateStudentSubmodules::dispatch($speciality);

        $speciality->modules()->sync($request->input('modules', []));        

        if ($isNew) {
            SpecialityPrice::createNewAsset($speciality->id);
        }
        
        $speciality_id = $speciality->id;
        

        if (!empty($inputs['id'])) {
            \Session::flash('flash_message', 'Changes Saved');
        } else {
            \Session::flash('flash_message', 'Added');
        }

        return redirect()->route('specialityEdit', ['id' => $speciality->id]);
    }
    
    private function updateSpecialityDisciplines($speciality_id, $requestAll) {
        $idsRow     = [];
        $disciplines = $this->specialityDisciplineDecorator($requestAll['discipline']);
        
        foreach($disciplines as $rowId => $disciplineArr) {
       $idsRow     = [];
             if ($disciplineArr['new_cloned'] == 1) {
                unset($disciplineArr['new_cloned']);
                $disciplineArr['speciality_id'] = $speciality_id;                
                $idsRow[] = DB::table('speciality_discipline')->insertGetId($disciplineArr);            
            } else {
               unset($disciplineArr['new_cloned']);
               DB::table('speciality_discipline')->where('id', $rowId)->update($disciplineArr);
               $idsRow[] = $rowId;
            }
        }       
        
        DB::table('speciality_discipline')->whereNotIn('id', $idsRow)->where('speciality_id', $speciality_id)->delete();
 
    }

    /**
     * @param $subjectList
     * @return array
     */
    public function specialitySubjectDecorator($subjectList)
    {
        $result = [];

        if (empty($subjectList)) {
            return $result;
        }

        foreach ($subjectList as $k => $item) {
            if (isset($item['visible']) && $item['visible'] == true)
                $result[] = [
                    'id' => $k,
                    'ent' => $item['ent']
                ];
        }

        return $result;
    }

    /**
     * @param $disciplineList
     * @return array
     */
    public function specialityDisciplineDecorator($disciplineList)
    {
        $result = [];

        if (!$disciplineList) {
            return $result;
        }

        foreach ($disciplineList as $k => $item) {
            if (isset($item['visible']) && $item['visible'] == true && isset($item['discipline_id'])) {

                $result[$k]['language_type']    = $item['language_type'] ?? 'native';
                $result[$k]['exam']             = (isset($item['exam']) && $item['exam'] == true) ? true : false;
                $result[$k]['has_coursework']   = (isset($item['has_coursework']) && $item['has_coursework'] == true) ? true : false;
                $result[$k]['discipline_cicle'] = $item['discipline_cicle'] ?? 'ООД';
                $result[$k]['mt_tk']            = $item['mt_tk'] ?? 'ОК';
                $result[$k]['pressmark']        = $item['pressmark'] ?? '';
                $result[$k]['semester']         = (isset($item['semester'])) ? $item['semester'] : null;
                $result[$k]['control_form']     = $item['control_form'] ?? 'test';
                $result[$k]['verbal_sro']       = $item['verbal_sro'] ?? '1';
                $result[$k]['sro_hours']        = $item['sro_hours'] ?? '1';
                $result[$k]['laboratory_hours'] = $item['laboratory_hours'] ?? '1';
                $result[$k]['practical_hours']  = $item['practical_hours'] ?? '1';
                $result[$k]['lecture_hours']    = $item['lecture_hours'] ?? '1';
                $result[$k]['cloned']           = $item['cloned'] ?? 0;
                $result[$k]['new_cloned']       = (isset($item['new_cloned'])) ? $item['new_cloned'] : 0;   
                $result[$k]['discipline_id']    = $item['discipline_id']; 
            }
        }

        return $result;
    }

    /**
     * @param $submodules
     * @return array
     */
    public function specialitySubmoduleDecorator($submodules)
    {
        $result = [];

        if (!$submodules) {
            return $result;
        }

        foreach ($submodules as $k => $item) {
            if (isset($item['visible']) && $item['visible'] == true) {
                $result[$k]['language_type'] = $item['language_type'] ?? 'native';
                $result[$k]['discipline_cicle'] = $item['discipline_cicle'] ?? 'ООД';
                $result[$k]['mt_tk'] = $item['mt_tk'] ?? 'ОК';
                $result[$k]['control_form'] = $item['control_form'] ?? 'test';
                $result[$k]['pressmark'] = $item['pressmark'] ?? '';                
                $result[$k]['semester'] = $item['semester'] ?? 1;
            }
        }

        return $result;
    }

    /**
     * Edit form
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $speciality = Speciality::getForEdit($id);

        $subjectList = Subject::get();
        $trendList = Trend::get();
        $entranceTestList = EntranceTest::get();
        $moduleList = Module::getListForSpecialityEdit();
        $disciplineCycles = Discipline::$cycles;
        $mtTks = Discipline::$mtTks;
        $languageTypes = Discipline::$languageTypes;
        $semesters = Semester::getSemestersList();
        $specialityDisciplines = $speciality->disciplines;

        return view('admin.pages.addeditSpeciality', compact(
            'speciality',
            'subjectList',
            'trendList',
            'entranceTestList',
            'moduleList',
            'disciplineCycles',
            'mtTks',
            'languageTypes',
            'semesters',
            'specialityDisciplines'
        ));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetListForSpecialityEdit( Request $request )
    {
        // validation data
        $obValidator = AdminAjaxGetListForSpecialityEditValidator::make( $request->all() );
        if ( $obValidator->fails() ) {
            return \Response::json([
                'status' => false,
                'message' => __('Error input data')
            ]);
        }

        if ($request->has('speciality_id')) {
            $speciality = Speciality::getById($request->get('speciality_id'));
        } else {
            $speciality = new Speciality();
        }

        $oModule = Module::getListForSpecialityEdit();

        $modules = [];

        foreach ($oModule as $module) {
            $in_speciality = $speciality->idInModules($module->id);

            foreach ($module->disciplines as $discipline) {
                if (request()->input('speciality_modules', 0) == 0 || $in_speciality) {
                    $modules[] = [
                        'type' => 'discipline',
                        'discipline_id' => $discipline->id,
                        'discipline_kge' => $speciality->getDisciplineExam($discipline->id, 0),
                        'module_id' => $module->id,
                        'module_name' => $module->name,
                        'discipline_name' => $discipline->name,
                        'discipline_semester' => $speciality->getDisciplineSemester($discipline->id),
                        'discipline_pressmark' => $speciality->getDisciplinePressmark($discipline->id, 'OK'),
                        'discipline_ects' => $discipline->ects,
                        'discipline_cicle' => $speciality->getDisciplineDisciplineCicle($discipline->id),
                        'discipline_mt_tk' => $speciality->getDisciplineMtTk($discipline->id),
                        'discipline_has_course_work' => $speciality->getDisciplineHasCoursework($discipline->id, 0),
                        'discipline_language_type' => $speciality->getDisciplineLangType($discipline->id),
                        'in_speciality' => $in_speciality,
                    ];
                }
            }

            foreach ($module->submodules as $submodule) {
                if (request()->input('speciality_modules', 0) == 1 || $in_speciality) {
                    $modules[] = [
                        'type' => 'submodule',
                        'discipline_id' => $submodule->id,
                        'discipline_kge' => null,
                        'module_id' => $module->id,
                        'module_name' => $module->name,
                        'discipline_name' => $submodule->name,
                        'discipline_semester' => $speciality->getSubmoduleSemester($submodule->id),
                        'discipline_pressmark' => $speciality->getSubmodulePressmark($submodule->id, 'OK'),
                        'discipline_ects' => $submodule->ects,
                        'discipline_cicle' => $speciality->getSubmoduleDisciplineCycle($submodule->id),
                        'discipline_mt_tk' => $speciality->getSubmoduleMtTk($submodule->id),
                        'discipline_has_course_work' => 0,
                        'discipline_language_type' => $speciality->getSubmoduleLangType($submodule->id),
                        'in_speciality' => $in_speciality,
                    ];
                }
            }
        }

        $modules = collect($modules);
        $totalRecords = $modules->count();

        if (request()->has('order')) {
            foreach (request()->get('order') as $item) {
                $dir = $item['dir'] == 'asc' ? true : false;

                $modules = $modules->sortBy(request()->get('columns')[$item['column']]['data'], SORT_REGULAR, $dir);
            }
        }

        $hasRightEdit = Auth::user()->hasRight('specialities', 'edit');

        return  Datatables::of($modules->values())
            ->editColumn('module_id', function ($module) use ($hasRightEdit) {
                $result = '';

                if ($hasRightEdit) {
                    $result = '<input ' .
                        ($module['in_speciality']? 'checked' : '') .
                        ' type="checkbox"' .
                        ' class="module_id"' .
                        ' value="' . $module['module_id'] . '">';
                }

                return $result;
            })
            ->editColumn('discipline_kge', function ($module) use ($hasRightEdit) {
                $data = '';

                if ($module['type'] == 'discipline') {
                    $data = '<input type="checkbox"' .
                        (!$hasRightEdit ? ' disabled' : '') .
                        ($module['discipline_kge'] ? ' checked' : '') .
                        ' id="exam-' . $module['discipline_id'] . '" onchange="changeDisciplineListExam(this, ' . $module['discipline_id'] . ')"/>';
                }

                return $data;
            })
            ->editColumn('discipline_name', function ($module) {
                if ($module['type'] == 'discipline') {
                    $data = '<a href="' . route('disciplineEdit', ['id' => $module['discipline_id']]) . '" target="_blank">' . $module['discipline_name'] . '</a>';
                } else {
                    $data = $module['discipline_name'];
                }

                return $data;
            })
            ->editColumn('discipline_semester', function ($module) use ($hasRightEdit) {
                $data['value'] = $module['discipline_semester'];
                $options = '';

                for ($i = 1; $i < 11; $i++) {
                    $options .= '<option value="' . $i . '" ' . ($module['discipline_semester'] == $i ? 'selected' : '') . '>' . $i . '</option>';
                }

                if ($module['type'] == 'discipline') {
                    $data['html'] = '<select ' . (!$hasRightEdit ? 'disabled' : '') . ' class="form-control" onchange="changeDisciplineSemester(this,' . $module['discipline_id'] . ')">' .
                        $options .
                        '</select>';
                } else {
                    $data['html'] = '<select ' . (!$hasRightEdit ? 'disabled' : '') . ' class="form-control" onchange="changeSubmoduleSemester(this,' . $module['discipline_id'] . ')">' .
                        $options .
                        '</select>';
                }

                return $data;
            })
            ->editColumn('discipline_pressmark', function ($module) use ($hasRightEdit) {
                if ($module['type'] == 'discipline') {
                    $data = '<input type="text"' .
                        (!$hasRightEdit ? ' disabled' : '') .
                        ' class="form-control" value="' . $module['discipline_pressmark'] . '"' .
                        ' id="pressmark-' . $module['discipline_id'] . '"'.
                        ' onchange="changeDisciplinePressmark(this, ' . $module['discipline_id'] . ')"/>';
                } else {
                    $data = '<input type="text"' .
                        (!$hasRightEdit ? ' disabled' : '') .
                        ' class="form-control" value="' . $module['discipline_pressmark'] . '"' .
                        ' id="pressmark-submodule-' . $module['discipline_id'] . '"'.
                        ' onchange="changeSubmodulePressmark(this, ' . $module['discipline_id'] . ')"/>';
                }

                return $data;
            })
            ->editColumn('discipline_ects', function ($module) {
                return $module['discipline_ects'] . '&nbsp;<sub>ECTS</sub>';
            })
            ->editColumn('discipline_has_course_work', function ($module) use ($hasRightEdit) {
                $data = [
                    'value' => $module['discipline_has_course_work'],
                    'html' => '',
                ];

                if ($module['type'] == 'discipline') {
                    $data['html'] = '<input type="checkbox"' .
                        (!$hasRightEdit ? ' disabled' : '') .
                        ($module['discipline_has_course_work'] ? ' checked' : '') .
                        ' name="has-coursework-' . $module['discipline_id'] . '"' .
                        ' onchange="changeDisciplineHasCoursework(this, ' . $module['discipline_id'] . ')">';
                }

                return $data;
            })
            ->editColumn('discipline_cicle', function ($module) use ($hasRightEdit) {
                $data['value'] = $module['discipline_cicle'];
                $options = '';

                $disciplineCycles = Discipline::$cycles;

                foreach($disciplineCycles as $disciplineCycle) {
                    $options .= '<option value="' . $disciplineCycle . '" ' . ($disciplineCycle == $module['discipline_cicle'] ? 'selected' : '') . '>'
                        . $disciplineCycle .
                        '</option>';
                }

                if ($module['type'] == 'discipline') {
                    $data['html'] = '<select ' . (!$hasRightEdit ? 'disabled' : '') . ' class="form-control" onchange="changeDisciplineDisciplineCicle(this,' . $module['discipline_id'] . ')">' .
                        $options .
                        '</select>';
                } else {
                    $data['html'] = '<select ' . (!$hasRightEdit ? 'disabled' : '') . ' class="form-control" onchange="changeSubmoduleDisciplineCicle(this,' . $module['discipline_id'] . ')">' .
                        $options .
                        '</select>';
                }

                return $data;
            })
            ->editColumn('discipline_mt_tk', function ($module) use ($hasRightEdit) {
                $data['value'] = $module['discipline_mt_tk'];
                $options = '';

                $mtTks = Discipline::$mtTks;

                foreach($mtTks as $mtTkItem) {
                    $options .= '<option value="' . $mtTkItem . '" ' . ($mtTkItem == $module['discipline_mt_tk'] ? 'selected' : '') . '>'
                        . $mtTkItem .
                        '</option>';
                }

                if ($module['type'] == 'discipline') {
                    $data['html'] = '<select ' . (!$hasRightEdit ? 'disabled' : '') . ' class="form-control" onchange="changeDisciplineMtTk(this,' . $module['discipline_id'] . ')">' .
                        $options .
                        '</select>';
                } else {
                    $data['html'] = '<select ' . (!$hasRightEdit ? 'disabled' : '') . ' class="form-control" onchange="changeSubmoduleMtTk(this,' . $module['discipline_id'] . ')">' .
                        $options .
                        '</select>';
                }

                return $data;
            })
            ->editColumn('discipline_language_type', function ($module) use ($hasRightEdit) {
                $data['value'] = $module['discipline_language_type'];
                $options = '';

                $languageTypes = Discipline::$languageTypes;

                foreach($languageTypes as $languageTypeKey => $languageType) {
                    $options .= '<option value="' . $languageTypeKey . '" ' . ($languageTypeKey == $module['discipline_language_type'] ? 'selected' : '') . '>'
                        . $languageType .
                        '</option>';
                }

                if ($module['type'] == 'discipline') {
                    $data['html'] = '<select ' . (!$hasRightEdit ? 'disabled' : '') . ' class="form-control" onchange="changeDisciplineMtTk(this,' . $module['discipline_id'] . ')">' .
                        $options .
                        '</select>';
                } else {
                    $data['html'] = '<select ' . (!$hasRightEdit ? 'disabled' : '') . ' class="form-control" onchange="changeSubmoduleMtTk(this,' . $module['discipline_id'] . ')">' .
                        $options .
                        '</select>';
                }

                return $data;
            })
            ->addColumn('dependence', function ($module) use ($hasRightEdit){
                if (request()->get('speciality_modules')){
                    $data =  '<button onclick="app.showDependenceModal('.$module['discipline_id'].')" 
                                      type="button"
                                      class="btn btn-default">
                                        <i class="fa fa-edit"></i>
                                         Добавить
                              </button>';
                } else {
                    if ($module['type'] == 'discipline') {
                        $data = '<input type="text"' .
                            (!$hasRightEdit ? ' disabled' : '') .
                            ' class="form-control" value="' . $module['discipline_pressmark'] . '"' .
                            ' id="pressmark-' . $module['discipline_id'] . '"'.
                            ' onchange="changeDisciplinePressmark(this, ' . $module['discipline_id'] . ')"/>';
                    } else {
                        $data = '<input type="text"' .
                            (!$hasRightEdit ? ' disabled' : '') .
                            ' class="form-control" value="' . $module['discipline_pressmark'] . '"' .
                            ' id="pressmark-submodule-' . $module['discipline_id'] . '"'.
                            ' onchange="changeSubmodulePressmark(this, ' . $module['discipline_id'] . ')"/>';
                    }
                }
                return $data;
            })
            ->setRowAttr([
                'style' => function($module) {
                    return $module['in_speciality'] ? 'background-color: #d6e9c6' : '';
                },
            ])
            //->setFilteredRecords($modules->values()->count())
            ->setTotalRecords($totalRecords)
            ->rawColumns([
                'module_id',
                'discipline_kge',
                'discipline_name',
                'discipline_semester.html',
                'discipline_pressmark',
                'discipline_ects',
                'discipline_has_course_work.html',
                'discipline_cicle.html',
                'discipline_mt_tk.html',
                'discipline_language_type.html',
                'dependence',
            ])
            ->make(true);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $speciality = Speciality::findOrFail($id);

        if ($speciality->profiles->count() > 0){
            \Session::flash('flash_error', 'У специальности есть зависимости!');

            return redirect()->back();
        }
        \File::delete(public_path() . '/images/uploads/specialities/' . $speciality->image_icon . '-b.jpg');
        \File::delete(public_path() . '/images/uploads/specialities/' . $speciality->image_icon . '-s.jpg');

        if ($speciality) {
            SpecialityDiscipline::where('speciality_id', $speciality->id)->delete();
            $speciality->delete();
        }

        \Session::flash('flash_message', 'Deleted');

        return redirect()->back();
    }

    /**
     * @param $specialityId
     * @param Request $request
     * @return mixed
     */
    public function exportKgePdf($specialityId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lang' => ['required', Rule::in(['ru', 'kz', 'en'])]
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        $speciality = Speciality::where('id', $specialityId)->first();

        if (!$speciality) {
            abort(404);
        }

        $quizeQuestions = $speciality->getKgeQuestionList($request->input('lang'));

        if (!$quizeQuestions) {
            $quizeQuestions = [];
        }

        ini_set('max_execution_time', 900);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('admin.pages.specialities.export.kge', compact('quizeQuestions', 'speciality'));

        return $pdf->download('export kge.pdf');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getModuleDisciplinesTable(Request $request)
    {
        $moduleId = $request->input('module_id');
        $specialityId = $request->input('speciality_id');

        $module = Module::getModuleWithDisciplinesAndSubmodulesLikeDisciplines($moduleId);

        $speciality = $specialityId ? Speciality::getById($specialityId) : new Speciality();

        $disciplineCycles = Discipline::$cycles;
        $mtTks = Discipline::$mtTks;
        $languageTypes = Discipline::$languageTypes;

        return view('admin/pages/specialities/moduleDisciplinesTable', compact('module', 'speciality', 'disciplineCycles', 'mtTks', 'languageTypes'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAllDisciplinesTable(Request $request)
    {
        $specialityId = $request->input('speciality_id');

        $speciality = $specialityId ? Speciality::where('id', $specialityId)->first() : new Speciality();
        $disciplineList = Discipline::get();

        return view('admin/pages/specialities/allDisciplinesList', compact('disciplineList', 'speciality'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByYear(Request $request)
    {
        $specList = Speciality::select(['id', 'name'])->where('year', $request->input('year'))->get();

        return Response::json($specList);
    }

    /**
     * @param $specialityId
     * @param $disciplineId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpecialityDisciplineSemester($specialityId, $disciplineId)
    {
        $specialityDiscipline = SpecialityDiscipline::where('discipline_id', $disciplineId)
            ->where('speciality_id', $specialityId)
            ->first();
        if (empty($specialityDiscipline)){
            $specialityDiscipline = new SpecialityDiscipline;
            $specialityDiscipline->speciality_id = $specialityId;
            $specialityDiscipline->discipline_id = $disciplineId;
            $specialityDiscipline->save();
        }

        $formatSemesters = [];
        foreach ($specialityDiscipline->disciplineSemesters as $semester){
            $formatSemesters[$semester->semester]['controlForm'][$semester->study_form] = $semester->control_form;
            $formatSemesters[$semester->semester]['hours'][$semester->study_form] = [
                'lecture' => $semester->lecture_hours,
                'practice' => $semester->practical_hours,
                'lab' => $semester->laboratory_hours,
                'sro' => $semester->sro_hours,
                'srop' => $semester->srop_hours,
            ];
            if ($specialityDiscipline->semesters->where('semester', $semester->semester)->isNotEmpty()){
                $formatSemesters[$semester->semester]['checked'] = true;
            } else {
                $formatSemesters[$semester->semester]['checked'] = false;
            }
        }
        $studentsSemester = [];
        foreach ($formatSemesters as $semester => $hours){
            $hours['name'] = $semester;
            $studentsSemester[] = $hours;
        }
        return response()->json($studentsSemester);
    }

    /**
     * @param AddDeleteSpecialityDisciplineSemesterRequest $request
     */
    public function addSpecialityDisciplineSemester(AddDeleteSpecialityDisciplineSemesterRequest $request): void
    {
        $specialityDiscipline = SpecialityDiscipline::where('discipline_id', $request->disciplineId)
            ->where('speciality_id', $request->specialityId)
            ->first();

        if (isset($specialityDiscipline)){
            $semester = $specialityDiscipline->semesters()
                ->where('semester', $request->semester)
                ->first();

            if (isset($semester) and $request->checked === false){
                $semester->delete();
            } elseif(empty($semester) and $request->checked){
                $semester = new SpecialityDisciplineSemester();
                $semester->speciality_discipline_id = $specialityDiscipline->id;
                $semester->semester = $request->semester;
                $semester->save();
            }
        }
    }

    /**
     * @param $specialityId
     * @param null $disciplineId
     * @return mixed
     * @throws \Exception
     */
    public function getDisciplineDependenceTable($specialityId, $disciplineId = null)
    {
        $specialityDisciplineDependencies = SpecialityDisciplineDependence::where('speciality_id', $specialityId)
            ->where('discipline_id', $disciplineId)
            ->get();

        $disciplineDependencies = [];
        $i = 1;
        foreach ($specialityDisciplineDependencies as $specialityDisciplineDependency){
            $specialityDisciplineDependency->num = $i;
            $disciplineDependencies[] = $specialityDisciplineDependency;
            $i++;
        }
        return Datatables::of($disciplineDependencies)
            ->addColumn('num', function($specialityDisciplineDependence){
                return $specialityDisciplineDependence->num;
            })
            ->addColumn('disciplines', function ($specialityDisciplineDependence) use ($specialityId){
                $html = "<select class='selectpicker_dependence' name='dependence[$specialityDisciplineDependence->id]' multiple>";

                $dependenceDisciplines = $specialityDisciplineDependence->dependenceDisciplines;
                $speciality = Speciality::find($specialityId);
                foreach ($speciality->disciplines as $specialityDiscipline){
                    $selected = $dependenceDisciplines->where('id', $specialityDiscipline->id)->count() > 0 ? 'selected' : '';

                    $html .= "<option $selected value='$specialityDiscipline->id'>$specialityDiscipline->name</option>";
                }
                return $html. '</select>';
            })
            ->addColumn('actions', function($specialityDisciplineDependence){
                $deleteBtn =  "<button class='btn btn-default' 
                                    type='button' onclick='app.deleteSpecialityDisciplineDependence($specialityDisciplineDependence->id)'>
                                    <i class='fa fa-trash'></i>
                                </button>";
                $saveButton = "<button class='btn btn-default-dark' 
                                    type='button' onclick='app.saveSpecialityDisciplineDependence($specialityDisciplineDependence->id)'>
                                    <i class='fa fa-save'></i>
                                </button>";

                return "<div class='btn btn-group'> $deleteBtn $saveButton </div>";
            })
            ->rawColumns(['disciplines', 'actions'])
            ->toJson();
    }

    /**
     * @param AddDependenceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSpecialityDisciplineDependence(AddDependenceRequest $request)
    {
        $specialityDisciplineDependence = new SpecialityDisciplineDependence();
        $specialityDisciplineDependence->speciality_id = $request->speciality_id;
        $specialityDisciplineDependence->discipline_id = $request->discipline_id;
        $specialityDisciplineDependence->year = $request->year;
        $specialityDisciplineDependence->save();

        return response()->json(['message' => 'Пререквизиты добавлены']);
    }

    /**
     * @param Request $request
     * @param $specialityDisciplineDependenceID
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSpecialityDisciplineDependence(Request $request, $specialityDisciplineDependenceID)
    {
        if ($request->has('dependence_disciplines_ids')){
            SpecialityDisciplineDependenceDiscipline::where('speciality_discipline_dependence_id', $specialityDisciplineDependenceID)
                ->delete();
            foreach ($request->dependence_disciplines_ids as $id){
                $dependence = new SpecialityDisciplineDependenceDiscipline();
                $dependence->speciality_discipline_dependence_id = $specialityDisciplineDependenceID;
                $dependence->discipline_id = $id;
                $dependence->save();
            }
        }
        return response()->json(['message' => 'Пререквизит сохранен']);
    }

    public function disciplineAdd(Request $request)
    {
        dumpLog($request);
        
    }
    
    public function disciplineEdit(Request $request)
    {
        $this->updateSpecialityDiscipline($request->all());        
    }
    
    public function disciplineDelete(Request $request)
    {
        
        
    }
    
    public function updateSpecialityDiscipline(array $request)
    {
        dumpLog($request);
        
        
        
         $idsRow     = [];
        $disciplines = $this->specialityDisciplineDecorator($requestAll['discipline']);
        
        foreach($disciplines as $rowId => $disciplineArr) {
       $idsRow     = [];
             if ($disciplineArr['new_cloned'] == 1) {
                unset($disciplineArr['new_cloned']);
                $disciplineArr['speciality_id'] = $speciality_id;                
                $idsRow[] = DB::table('speciality_discipline')->insertGetId($disciplineArr);            
            } else {
               unset($disciplineArr['new_cloned']);
               DB::table('speciality_discipline')->where('id', $rowId)->update($disciplineArr);
               $idsRow[] = $rowId;
            }
        }       
        
        DB::table('speciality_discipline')->whereNotIn('id', $idsRow)->where('speciality_id', $speciality_id)->delete();
 
        
        
    }
    
    
    
   
    /**
     * @param $specialityDisciplineDependenceID
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSpecialityDisciplineDependence($specialityDisciplineDependenceID)
    {
        SpecialityDisciplineDependenceDiscipline::where('speciality_discipline_dependence_id', $specialityDisciplineDependenceID)
            ->delete();
        SpecialityDisciplineDependence::where('id', $specialityDisciplineDependenceID)
            ->delete();

        return response()->json(['message' => 'Пререквизит удвлен']);
    }
}
