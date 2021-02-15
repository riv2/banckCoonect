<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Mail;
use App\{AdminUserDiscipline,
    Discipline,
    EmployeesDepartment,
    Mail\EmployeesEditProfile,
    Notification,
    Services\Auth,
    User,
    DocsEnquire,
    ManualPerk,
    ManualWorkShedule,
    ManualOrganization,
    EmployeesUser,
    EmployeesFile,
    EmployeesOrder,
    EmployeesVacancy,
    EmployeesPosition,
    EmployeesUsersPerk,
    EmployeesRequirement,
    EmployeesUsersResume,
    EmployeesUsersDecree,
    EmployeesUserTeacher,
    EmployeesUsersPosition,
    EmployeesUserEducation,
    EmployeesUserRequirement,
    EmployeesUserPublication,
    EmployeesUsersSocialPackage
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Services\EmployeesDecree;
use App\Http\Controllers\Controller;
use App\Validators\{
    UserVacancyFormValidation,
    EmployeesUsersVerdictValidation,
    EmployeesUserPositionsValidation,
    EmployeesUserEducationValidation,
    EmployeesUserPublicationValidation,
    EmployeesUserSocialPackageValidation
};

class EmployeesUsersController extends Controller
{
    public function index(){
        $positions = EmployeesPosition::all();
        $orders    = EmployeesOrder::where('status', 'new')
            ->whereHas('orderName', function($query) {
                $query->where('code', '!=', 'recruitment');
            })
            ->get();

        return view('admin.pages.employees.users.index', compact('orders', 'positions'));
    }

    /**
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editEmployeePage($id = null){
        $positions = null;
        $vacancies = EmployeesVacancy::all();
        $work_shedule = ManualWorkShedule::all();
        $perks = ManualPerk::all();
        $isTeacher = null;


        if(isset($id)){
            $user = EmployeesUser::where('user_id', $id)->first();
            $requirements = null;
            $socialPackage = $user->socialPackage;
            $userPositions = $user->employeesUserPositions()->get();
            $positions = [];

            foreach ($userPositions as $value) {
                $positions[$value->position_id] = $value->position->name;
                if ($value->hasTeacherRole){
                    $isTeacher = true;
                }
            }
            return view('admin.pages.employees.users.edit', compact(
                'requirements',
                'user',
                'positions',
                'vacancies',
                'id',
                'work_shedule',
                'perks',
                'socialPackage',
                'isTeacher'
            ));
        } else {
            $user = null;
            $requirements  = EmployeesRequirement::all();

            return view('admin.pages.employees.users.create', compact(
                'requirements', 'user', 'vacancies'
            ));
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPositionRequirements(Request $request){
        $vacancy = EmployeesVacancy::where('id', $request->vacancy_id)->first();
        $unsortRequirements = $vacancy->position->positionRequirements()->with('fields')->get();
        $requirements['personal_info'] = [];
        $requirements['education'] = [];
        $requirements['qualification_increase'] = [];
        $requirements['seniority'] = [];
        $requirements['nir'] = [];
        $requirements['army'] = [];

        foreach ($unsortRequirements as $key => $value) {
            $requirements[$value->category] += [ $value->name => [$value->toArray()] ];
        }

        return response()->json(['status' => 'success', 'requirements' => $requirements]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPositionRequirements(Request $request){
        if ($request->type == 'resume') {
            $resume = EmployeesUsersResume::where('user_id', $request->user_id)
                ->where('vacancy_id', $request->vacancy_id)
                ->first();

            $resumeRequirements = $resume->requirements()
                ->with('requirement')
                ->with('requirement.fields')
                ->get();

            $requirements['personal_info'] = [];
            $requirements['education'] = [];
            $requirements['qualification_increase'] = [];
            $requirements['seniority'] = [];
            $requirements['nir'] = [];
            $requirements['army'] = [];

            foreach ($resumeRequirements as $key => $value) {
                $requirements[$value->requirement->category] += [ $key => $value->toArray() ];
            }
        }

        if ($request->type == 'resume') {
            $vacancy = EmployeesVacancy::where('id', $request->vacancy_id)->first();
            $userPosition = EmployeesUsersPosition::where('user_id', $request->user_id)->where('position_id', $vacancy->position->id)->first();
            $position_id = $userPosition->id;
        } else {
            $userPosition = EmployeesUsersPosition::where('user_id', $request->user_id)->where('position_id', $request->vacancy_id)->first();
            $position_id = $userPosition->id;
        }

        $userPosition->organization = ManualOrganization::where('id', $userPosition->organization)->value('name');
        $perks = $userPosition->perks()->pluck('perk_id')->toArray();

        return response()
            ->json(
                [
                    'status' => 'success',
                    'requirements' => $requirements ?? "empty",
                    'userPosition' => $userPosition,
                    'perks' => $perks,
                    'position_id' => $position_id
                ]
            );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createEmployees(Request $request){
        /*** Validation ***/
        $aRuleList = [];
        $aMessageList = [];
        $vacancy = EmployeesVacancy::where('id', $request->vacancy_id)->first();
        $requirements = $vacancy->position->positionRequirements()->with('fields')->get()->toArray();

        foreach ($requirements as $value) {
            if($value['field_type'] != 'json'){
                $aMessageList += ['requirements.'.$value['category'].'.'.$value['field_name'] => $value['name']];
            } else {
                foreach ($value['fields'] as $val) {
                    $aMessageList += ['requirements.'.$value['category'].'.'.$val['field_name'] => $val['name']];
                }
            }

        }

        foreach ($requirements as $key => $value) {
            if($value['field_type'] != 'json'){
                $rule = $value['field_type'] == 'text' || $value['field_type'] == 'date' || $value['field_type'] == 'select' ? '|max:255' : '|max:10240';
                $field = $value['field_type'] == 'text' ? 'string' : $value['field_type'];
                if($value['field_name'] != 'json'){
                    $aRuleList += ['requirements.'.$value['category'].'.'.$value['field_name'] => 'nullable|'.$field.$rule];
                }
            } else {
                foreach ($value['fields'] as $val) {
                    $rule = $val['field_type'] == 'text' || $val['field_type'] == 'date' || $val['field_type'] == 'select' ? '|max:255' : '|max:10240';
                    $field = $val['field_type'] == 'text' ? 'string' : $val['field_type'];
                    if($val['field_name'] != 'json'){
                        $aRuleList += ['requirements.'.$value['category'].'.'.$val['field_name'] => 'nullable|'.$field.$rule];
                    }
                }
            }

        }

        $validator = UserVacancyFormValidation::make($request->all(), $aRuleList, [], $aMessageList);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        /*** Create user requirements and employeesUser ***/
        $user = User::where('id', $request->user_id)->first();

        if (empty($user)) {
            return redirect()->back()->withErrors(['Не удалось найти пользователя']);
        }
        $resume = EmployeesUsersResume::create([
            'user_id'    => $user->id,
            'vacancy_id' => $request->vacancy_id,
            'status'     => 'approved'
        ]);

        foreach ($request->get('requirements', []) as $category => $category_value) {
            if($category == 'personal_info'){
                foreach($category_value as $requirementID => $field_content){
                    if(gettype($field_content) == 'object'){
                        $fileName = time().'_'.$field_content->getClientOriginalName();
                        $field_content->move(storage_path('app/employees/users/requirements/'), $fileName);
                        $field_content = $fileName;
                    }
                    EmployeesUserRequirement::create([
                        'resume_id'      => $resume->id,
                        'requirement_id' => $requirementID,
                        'content'        => $field_content?? ''
                    ]);
                }
            } else {
                foreach($category_value as $requirementID => $requirement_duplicates){
                    $json = [];
                    foreach ($requirement_duplicates as $requirement_index => $requirement_fields) {
                        foreach ($requirement_fields as $requirement_field_name => $requirement_field_content) {
                            if(gettype($requirement_field_content) == 'object'){
                                $fileName = time().'_'.$requirement_field_content->getClientOriginalName();
                                $requirement_field_content->move(storage_path('app/employees/users/requirements/'), $fileName);
                                $json[$requirement_index][$requirement_field_name] = $fileName;
                            } else {
                                $json[$requirement_index][$requirement_field_name] = $requirement_field_content;
                            }
                        }

                    }
                    EmployeesUserRequirement::create([
                        'resume_id'      => $resume->id,
                        'requirement_id' => $requirementID,
                        'content'        => 'json_content',
                        'json_content'   => json_encode($json)
                    ]);
                }
            }
        }

        $user_position = EmployeesUsersPosition::create([
            'user_id' => $request->user_id,
            'position_id' => $vacancy->position_id
        ]);

        $employee = EmployeesUser::where('user_id', $user->id)->first();

        if(empty($employee)){
            EmployeesUser::create([
                'user_id' => $user->id,
                'status' => 'сотрудник'
            ]);
        }

        return redirect()->route('employees.user.edit.position', ['user_id' => $request->user_id, 'position_id' => $user_position->id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editEmployees(Request $request){
        $resume = EmployeesUsersResume::where('user_id', $request->user_id)->where('vacancy_id', $request->vacancy_id)->first();

        foreach ($request->requirements as $category => $category_value) {
            if($category == 'personal_info'){
                foreach($category_value as $requirementID => $field_content){
                    if(gettype($field_content) == 'object'){
                        $fileName = time().'_'.$field_content->getClientOriginalName();
                        $field_content->move(storage_path('app/employees/users/requirements/'), $fileName);
                        $field_content = $fileName;
                    }
                    EmployeesUserRequirement::where('resume_id', $resume->id)->where('requirement_id', $requirementID)->update([
                        'content' => $field_content?? ''
                    ]);
                }
            } else {
                foreach($category_value as $requirementID => $requirement_duplicates){
                    $json = [];
                    foreach ($requirement_duplicates as $requirement_index => $requirement_fields) {
                        foreach ($requirement_fields as $requirement_field_name => $requirement_field_content) {
                            if(gettype($requirement_field_content) == 'object'){
                                $fileName = time().'_'.$requirement_field_content->getClientOriginalName();
                                $requirement_field_content->move(storage_path('app/employees/users/requirements/'), $fileName);
                                $json[$requirement_index][$requirement_field_name] = $fileName;
                            } else {
                                $json[$requirement_index][$requirement_field_name] = $requirement_field_content;
                            }
                        }

                    }
                    $requirement = EmployeesUserRequirement::where('resume_id', $resume->id)->where('requirement_id', $requirementID)->first();
                    $requirement->json_content = json_encode($json);
                    $requirement->save();
                }
            }
        }

        return redirect()->route('employeesUsers');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUser(Request $request){
        $userResult = [];
        $result = User::where('email', 'like', '%' . $request['query'] . '%')->get();

        if ($result->count() > 0) {
            foreach ($result as $value) {
                $userResult[] = '<div class="btn searchEmail w-100 text-left">'.$value->email.'</div><br>';
            }
        }

        return response()->json($userResult);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserData(Request $request){
        $user = User::where('email', $request->email)->first();
        if(!empty($user)){
            $profile = $user->studentProfile;
            $doctype = DocsEnquire::where('user_id', $user->id)->value('doctype');

            return response()->json(['status' => 'profile', 'user' => $user, 'profile' => $profile ]);
        }

        return response()->json(['status' => 'empty']);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function employeesDatatable(Request $request){
        $records = EmployeesUser::where('status', '!=', EmployeesUser::STATUS_CANDIDATE)
            ->has('user');

        return Datatables::of($records)
            ->addColumn('name', function($record){
                if($record->user){
                    foreach ($record->requirements as $value) {
                        if(isset($value->requirement) and $value->requirement->field_name == 'fio_ru'){
                            return $value->content;
                        }
                    }

                    return $record->user->name == '' ? $record->user->fio : $record->user->name;
                } else {
                    return 'Юзер удалён';
                }
            })
            ->addColumn('iin', function($record){
                $iin = '';
                if($record->user){
                    if(isset($record->user->studentProfile)){
                        $iin = $record->user->studentProfile->iin;
                    } elseif(isset($record->user->teacherProfile)){
                        $iin = $record->user->teacherProfile->iin;
                    }
                    return $iin;
                } else {
                    return 'Юзер удалён';
                }
            })
            ->addColumn('department', function($record){
                $array = [];
                if($record->user){
                    $positions = $record->user->positions;
                    foreach($positions as $position){
                        if(isset($position->position) && isset($position->position->department)){
                            array_push($array, $position->position->department->name);
                        } else {
                            array_push($array, 'Отдела не существует');
                        }
                    }
                    $array = array_unique($array);

                    return $str = implode (", ", $array);
                } else {
                    return 'Юзер удалён';
                }
            })
            ->addColumn('position', function($record){
                $array = [];
                if($record->user){
                    $positions = $record->user->positions;
                    foreach($positions as $position){
                        if(isset($position->position)){
                            array_push($array, $position->position->name);
                        } else {
                            array_push($array, 'Должность удалена');
                        }
                    }
                    $array = array_unique($array);

                    return $str = implode (", ", $array);
                } else {
                    return 'Юзер удалён';
                }
            })
            ->addColumn('action', function ($record){
                if($record->user){
                    return '<div class="text-center">
                            <a href="'.route("addNewEmployee", ["id" => $record->user_id]).'"><button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Редактировать"><i class="md md-edit"></i></button></a>
                            <a href="'.route("employees.user.positions.page", ["id" => $record->user_id]).'"><button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Связать с должностями"><i class="md md-list"></i></button></a>
                            <a href="'.route("employees.user.social.package", ["id" => $record->user_id]).'"><button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Социальный пакет"><i class="md md-shopping-basket"></i></button></a>
                            <input class="form-check-input" type="checkbox" name="selectUserList" value="'.$record->id.'">
                        </div>';
                } else {
                    return 'Юзер удалён';
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userSocialPackage($id){
        $user = User::where('id', $id)->first();
        $employeesUser = EmployeesUser::where('user_id', $id)->first();
        $package = $employeesUser->socialPackage;

        return view('admin.pages.employees.users.socialPackage', compact('id', 'user', 'package'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editUserSocialPackage(Request $request){
        $validator = EmployeesUserSocialPackageValidation::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        $array = [
            'gas'       => $request->gas,
            'basket'    => $request->basket,
            'medicines' => $request->medicines,
            'cellular'  => $request->cellular,
            'taxi'      => $request->taxi
        ];

        if(isset($request->food)){
            $array['food'] = true;
        }

        $employees = EmployeesUser::where('user_id', $request->employees_user_id)->first();
        EmployeesUsersSocialPackage::updateOrCreate(['employees_user_id' => $employees->id], $array);

        return redirect()->route('employeesUsers');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userPositionsPage($id){
        $user = User::where('id', $id)->first();
        $positions = EmployeesPosition::all();
        $work_shedule = ManualWorkShedule::all();

        return view('admin.pages.employees.users.positions', compact('user', 'positions', 'work_shedule'));
    }

    /**
     * @param $user_id
     * @param $position_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editUserPositionPage($user_id, $position_id){
        $organizations = ManualOrganization::all();
        $user = User::where('id', $user_id)->first();
        $user_position = EmployeesUsersPosition::where('id', $position_id)->first();
        $user_position_perks = $user_position->perks->pluck('perk_id');
        $work_shedule = ManualWorkShedule::all();
        $perks = ManualPerk::all();

        return view(
            'admin.pages.employees.users.editPosition',
            compact('user_position_perks', 'user_position', 'perks', 'user', 'work_shedule', 'organizations')
        );
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function userPositionsDatatable(Request $request){
        $records = EmployeesUsersPosition::where('user_id', $request->id)->get();

        return Datatables::of($records)
            ->addColumn('position_id', function ($record){
                return EmployeesPosition::where('id', $record->position_id)->value('name');
            })
            ->addColumn('schedule', function ($record){
                return ManualWorkShedule::where('id', $record->schedule)->value('name');
            })
            ->addColumn('organization', function ($record){
                return ManualOrganization::where('id', $record->organization)->value('name');
            })
            ->addColumn('action', function ($record) use ($request) {
                return '<div class="text-center">
                            <a href="'.route("employees.user.edit.position", ["user_id" => $request->id, "position_id" => $record->id]).'" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Редактировать запись"><i class="md md-edit"></i></a>
                            <a href="'.route("employees.user.delete.position", ["id" => $record->id]).'"><button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Удалить должность"><i class="fa fa-trash"></i></button></a>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editLinkUserPosition(Request $request){
        $request->request->add(['editPosition' => 'on']);
        $validator = EmployeesUserPositionsValidation::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        EmployeesUsersPosition::where('id', $request->user_position_id)->update([
            'employment'      => $request->employment,
            'price'           => $request->price,
            'salary'          => $request->salary,
            'organization'    => $request->organization,
            'payroll_type'    => $request->payroll_type,
            'schedule'        => $request->schedule,
            'employment_form' => $request->employment_form,
            'probation_from'  => $request->probation_from,
            'probation_to'    => $request->probation_to,
            'premium'         => $request->premium,
        ]);

        if(isset($request->perks)){
            EmployeesUsersPerk::where('employees_position_id', $request->user_position_id)->delete();
            foreach ($request->perks as $key => $value) {
                EmployeesUsersPerk::create([
                    'employees_position_id' => $request->user_position_id,
                    'perk_id'               => $value
                ]);
            }
        }

        return redirect()->route('employees.user.positions.page', ['id' => $request->user_id]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userDeletePosition($id){
        $position = EmployeesUsersPosition::where('id', $id)->first();
        AdminUserDiscipline::where('employees_user_position_id', $position->position_id)
            ->delete();

        $position->delete();
        return back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function candidatesPage(){
        $orders = EmployeesOrder::where('status', 'new')
            ->whereHas('orderName', function($query) {
                $query->where('code', 'recruitment');
            })
            ->get();

        return view('admin.pages.employees.candidates.index', compact('orders'));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function candidatesDatatable(Request $request){
        $records = EmployeesUsersResume::where('status', '!=', 'approved')
            ->orWhere('updated_at', '>=', Carbon::yesterday())
            ->orderBy('updated_at', 'desc');

        return Datatables::of($records)
            ->addColumn('id', function($record){
                return $record->user_id;
            })
            ->addColumn('name', function($record){
                $response = '';
                $fio = EmployeesUserRequirement::where('resume_id', $record->id)->whereHas('requirement', function($query){
                    $query->where('name', 'ФИО (рус)');
                })->first();

                if (isset($fio)) {
                    $response = $fio->content;
                } else {
                    $response = empty($record->user->studentProfile->fio) ? 'Имя не указано в профиле' : $record->user->studentProfile->fio;
                }

                return $response;
            })
            ->addColumn('position', function($record){
                return isset($record->vacancy)
                    ? isset($record->vacancy->position)
                        ? $record->vacancy->position->name
                        : 'Должность удалена'
                    : 'Вакансия удалена';
            })
            ->addColumn('status', function($record){
                $statuses = EmployeesUsersResume::$statuses;
                return $statuses[$record->status];
            })
            ->addColumn('action', function ($record){
                if(isset($record->vacancy) && isset($record->vacancy->position)){
                    $str = '<div class="text-center">';
                    switch ($record->status) {
                        case 'pending':
                            $userPosition = EmployeesUsersPosition::where('user_id', $record->user_id)
                                ->where('position_id', $record->vacancy->position->id)
                                ->first();
                            if (!empty($userPosition)) {
                                $str .= '<a href="'.route("employees.show.candidate.resume", ["id" => $record->id, "type" => "edit"]).'">
                                            <button 
                                                class="btn btn-default" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Посмотреть отредактированные требования"
                                            >
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </a>';
                            } else {
                                $str .= '<a href="'.route("employees.show.candidate.resume", ["id" => $record->id]).'">
                                            <button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Посмотреть резюме">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </a>';
                            }

                            break;

                        case 'interview':
                            $str .= '<a href="'.route("employees.show.candidate.resume", ["id" => $record->id]).'">
                                        <button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Посмотреть резюме">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </a>
                                    
                                    <button 
                                        class="btn btn-default verdictResume" 
                                        data-toggle="tooltip" 
                                        data-placement="top" 
                                        title="Принять"
                                        data-resume-id="'.$record->id.'" 
                                        data-verdict="approved"
                                    >
                                        <i class="fa fa-check"></i>
                                    </button>
                                    <button 
                                        class="btn btn-default verdictResume" 
                                        data-toggle="tooltip" 
                                        data-placement="top" 
                                        title="Отклонить" 
                                        data-resume-id="'.$record->id.'" 
                                        data-verdict="declined"
                                    >
                                        <i class="fa fa-close"></i>
                                    </button>';
                            break;

                        case 'approved':
                            $str .= '<input class="form-check-input ml-15" type="checkbox" name="selectUserList" value="'.$record->id.'">';
                            break;

                        default:
                            $str = '';
                            break;
                    }
                    $str .= '</div>';

                    return $str;
                } else{
                    return '';
                }

            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * @param $id
     * @param null $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function candidatesShowResume($id, $type = null){
        $resume = EmployeesUsersResume::where('id', $id)->first();
        $resumeRequirements = $resume->requirements()->with('requirement')->with('requirement.fields')->get();
        $requirements['personal_info'] = [];
        $requirements['education'] = [];
        $requirements['qualification_increase'] = [];
        $requirements['nir'] = [];
        $requirements['seniority'] = [];
        $requirements['army'] = [];

        foreach ($resumeRequirements as $key => $value) {
            $requirements[$value->requirement->category] += [ $key => $value->toArray() ];
        }

        return view('admin.pages.employees.candidates.candidateResume', compact('resume', 'requirements', 'type'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verdictCandidatesResume(Request $request){
        $resume = EmployeesUsersResume::where('id', $request->resume_id)->first();
        $status = '';
        $reason = '';

        if($request->verdict == 'approved'){
            $resume->update([
                'status' => 'interview'
            ]);

            $status = 'одобрено';
        } elseif($request->verdict == 'declined'){
            $validator = EmployeesUsersVerdictValidation::make($request->all());
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->messages());
            }

            $resume->update([
                'status' => 'declined',
                'reason' => $request->reason
            ]);

            $status = 'отклонено';
            $reason = $request->reason;
        } elseif($request->verdict == 'revision'){
            $validator = EmployeesUsersVerdictValidation::make($request->all());
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->messages());
            }

            $resume->update([
                'status' => 'revision_position_requirements',
                'reason' => $request->reason
            ]);

            $status = 'Требует доработки';
            $reason = $request->reason;
        } elseif($request->verdict == 'approvedEdit'){
            $resume->update([ 'status' => 'approved' ]);
        }

        if($request->verdict != 'approvedEdit'){
            $email = '';
            $userEmail = EmployeesUserRequirement::where('resume_id', $resume->id)->whereHas('requirement', function($query){
                $query->where('name', 'Email');
            })->first();
            if(!empty($email) && $email->content != '' && filter_var($email->content, FILTER_VALIDATE_EMAIL)){
                $email = $userEmail;
            } elseif(filter_var($resume->user->email, FILTER_VALIDATE_EMAIL)) {
                $email = $resume->user->email;
            }

            if($email != ''){
                Mail::send('emails.employees.interview_invitation',
                    [
                        'vacancy' => $resume->vacancy->position->name,
                        'status'  => $status,
                        'reason'  => $reason
                    ],
                    function ($message) use ($email) {
                        $message->from(getcong('site_email'), getcong('site_name'));
                        $message->to($email)->subject('Заявка на вакансию одобрена');
                    }
                );
            }
        }

        return redirect()->route('employees.candidates');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verdictCandidatesInterview(Request $request){
        $resume = EmployeesUsersResume::where('id', $request->resume_id)->first();
        $reason = '';
        $decree = [];

        if($request->verdict == 'approved'){
            $position = $resume->vacancy->position;
            $resume->update([ 'status' => $request->verdict ]);
            $user_position = EmployeesUsersPosition::create([
                'user_id' => $resume->user_id,
                'position_id' => $position->id
            ]);

            $decree['user_name'] = $resume->user->name;
            $decree['position_name'] = $position->name;
            $employeesDecree = new EmployeesDecree();
            $response = $employeesDecree->approveCandidate($decree);
            EmployeesUsersDecree::create([
                'user_id'  => $resume->user->id,
                'name'     => $response,
                'doc_type' => 'recruitment'
            ]);

        } else {
            $validator = EmployeesUsersVerdictValidation::make($request->all());
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->messages());
            }

            $resume->update([
                'status' => $request->verdict,
                'reason' => $request->reason
            ]);

            $reason = $request->reason;
        }

        $email = '';
        $userEmail = EmployeesUserRequirement::where('resume_id', $resume->id)->whereHas('requirement', function($query){
            $query->where('name', 'Email');
        })->first();
        if(!empty($email) && $email->content != '' && filter_var($email->content, FILTER_VALIDATE_EMAIL)){
            $email = $userEmail;
        } elseif(filter_var($resume->user->email, FILTER_VALIDATE_EMAIL)) {
            $email = $resume->user->email;
        }

        if($email != ''){
            Mail::send('emails.employees.user_approved',
                [
                    'position' => $resume->vacancy->position->name,
                    'status'   => $request->verdict,
                    'reason'   => $reason
                ],
                function ($message) use ($email) {
                    $message->from(getcong('site_email'), getcong('site_name'));
                    $message->to($email)->subject('Ответ по заявке на работу');
                }
            );
        }

        if($request->verdict == 'approve'){
            return redirect()->route('employees.user.edit.position', ['user_id' => $resume->user_id, 'position_id' => $user_position->id]);
        } else {
            return redirect()->route('employees.candidates');
        }
    }

    /**
     * @param $name
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFile($name){
        $file = EmployeesFile::where('file_hash', $name)->first();
        $return_file = storage_path('app/employees/users/requirements/').$file['file_name'];

        return response()->download($return_file);
    }

    /**
     * @param $vacancyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPositionDisciplines($vacancyId)
    {
        $vacancy = EmployeesVacancy::find($vacancyId);
        $positionId = $vacancyId;
        if (isset($vacancy)){
            $positionId = $vacancy->position_id;
        }
        $position = EmployeesPosition::find($positionId);
        if ($position->department->is_sector){
            return response()->json([
                'position_is_discipline' => true,
                'department_id' => $position->department_id
            ]);
        }
        return response()->json([
            'position_is_discipline' => false,
            'department_id' => $position->department_id
        ]);
    }

    /**
     * @param $id
     * @param $sectorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function userDisciplinesList($id, $sectorId)
    {
        $employeesDisciplines = AdminUserDiscipline::where('user_id', $id)
            ->get();


        $employeesDisciplinesIds = [];
        foreach ($employeesDisciplines as $employeesDiscipline){
            $employeesDisciplinesIds[] = $employeesDiscipline->discipline_id;
        }

        $disciplines = Discipline::where('sector_id', $sectorId)
            ->select(['id', 'name'])
            ->whereNotIn('id',$employeesDisciplinesIds)
            ->get();

        return response()->json($disciplines);
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function userDisciplinesDatatable($id)
    {
        $employeesDisciplines = AdminUserDiscipline::where('user_id', $id);

        return Datatables::of($employeesDisciplines)
            ->filterColumn('lang', function($query, $search){
                $lang = 'kz_lang';
                if ($search === 'ru'){
                    $lang = 'ru_lang';
                }
                if ($search === 'en'){
                    $lang = 'en_lang';
                }
                $query->where($lang, true);
            })
            ->filterColumn('name', function ($query, $search){
                $query->whereHas('discipline', function($q) use ($search){
                    $q->where('name', 'like', "%$search%");
                });
            })
            ->filterColumn('credits', function ($query, $search){
                $query->whereHas('discipline', function($q) use ($search){
                    $q->where('credits', $search);
                });
            })
            ->addColumn('name', function($discipline){
                return $discipline->discipline->name;
            })
            ->addColumn('credits', function ($discipline){
                return $discipline->discipline->credits;
            })
            ->addColumn('lang', function($discipline) {
                $str = '<select multiple class="selectpiker" name="disciplines['.$discipline->discipline_id.'][lang][]">';

                $kzCheck = $discipline->kz_lang ? 'selected' : '';
                $ruCheck = $discipline->ru_lang ? 'selected' : '';
                $enCheck = $discipline->en_lang ? 'selected' : '';

                $str .= '<option '.$kzCheck.' value="kz_lang">Казахский</option>';
                $str .= '<option '.$ruCheck.' value="ru_lang">Русский</option>';
                $str .= '<option '.$enCheck.' value="en_lang">Английский</option>';

                return $str.'</select>';
            })
            ->addColumn('actions', function($discipline){
                return '<button type="button" onclick="main.deleteDiscipline('.$discipline->discipline_id.', this)" class="btn btn-default-dark"><i class="md md-delete"></i></button>';
            })
            ->rawColumns(['actions', 'lang'])
            ->toJson();
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function addDisciplines(Request $request, $id)
    {
        $vacancy = EmployeesVacancy::find($request->vacancy);
        $positionId = $request->vacancy;
        if (isset($vacancy)){
            $positionId = $vacancy->position_id;
        }
        if ($request->has('disciplines')){
            foreach ($request->get('disciplines') as $discipline){
                $adminDiscipline = AdminUserDiscipline::where('discipline_id', $discipline['id'])
                    ->where('user_id', $id)
                    ->first();

                if (empty($adminDiscipline)){
                    $teacherDiscipline = new AdminUserDiscipline();
                    $teacherDiscipline->user_id = $id;
                    $teacherDiscipline->discipline_id = $discipline['id'];
                    $teacherDiscipline->employees_user_position_id = $positionId;
                    $teacherDiscipline->save();
                }
            }
        }
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function deleteEmployeesDiscipline(Request $request, $id): void
    {
        AdminUserDiscipline::where('user_id', $id)
            ->where('discipline_id', $request->disciplineId)
            ->delete();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDisciplines(Request $request, $id): ?\Illuminate\Http\JsonResponse
    {
        try {
            $disciplines = $request->get('disciplines', null);

            if (isset($disciplines)){
                foreach ($disciplines as $disciplineId => $values){
                    $adminDiscipline = AdminUserDiscipline::where('user_id', $id)
                        ->where('discipline_id', $disciplineId)
                        ->first();

                    foreach (AdminUserDiscipline::LANGS as $lang){
                        if (in_array($lang, $values['lang'])){
                            $adminDiscipline->$lang = true;
                        } else {
                            $adminDiscipline->$lang = false;
                        }
                    }
                    $adminDiscipline->save();
                }
            }
        } catch (Exception $e){
            return response()->json($e, 422);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployerVacancyRequirements(Request $request){
        $resume = EmployeesUsersResume::where('user_id', $request->user_id)
            ->where('vacancy_id', $request->vacancy_id)
            ->first();
        $positionFields = $resume->vacancy->position->positionRequirements()->with('fields')->get();

        $resumeRequirements = $resume->requirements()
            ->with('requirement')
            ->with('requirement.fields')
            ->get();
        $requirements['personal_info'] = [];
        $requirements['education'] = [];
        $requirements['qualification_increase'] = [];
        $requirements['seniority'] = [];
        $requirements['nir'] = [];

        foreach ($positionFields as $key => $value) {
            if (!in_array($value->field_name, EmployeesUserRequirement::PROTECTED_EDIT_FIELDS)){
                $requirement = $value->toArray();
                $requirement['content'] = '';
                $requirements[$value->category][$value->id] = $requirement;
            }
        }
        foreach ($resumeRequirements as $key => $value) {
            if (isset($value->requirement) and !in_array($value->requirement->field_name, EmployeesUserRequirement::PROTECTED_EDIT_FIELDS)){
                $requirement = $value->requirement->toArray();
                $requirement['content'] = $value->content;

                $requirements[$value->requirement->category][$value->requirement->id] = $requirement;
            }
        }
        return response()->json($requirements);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveEmployerVacancyRequirements(Request $request)
    {
        $resume = EmployeesUsersResume::where('user_id', Auth::id())
            ->where('vacancy_id', $request->vacancy_id)
            ->first();
        $updatedFields = [];

        if ($request->has('requirements') and isset($resume)){
            foreach ($request->requirements as $requirementId => $fieldValue){
                $resumeField = $resume->requirements()->where('requirement_id', $requirementId)->first();

                $requirement = EmployeesRequirement::find($requirementId);
                if (isset($fieldValue)){
                    if (empty($resumeField)){

                        $updatedFields[$requirement->name] = $fieldValue;

                        $newResumeField = new EmployeesUserRequirement();
                        $newResumeField->resume_id = $resume->id;
                        $newResumeField->content = $fieldValue ?? '';
                        $newResumeField->requirement_id = $requirementId;
                        $newResumeField->save();

                    } elseif($resumeField->content !== $fieldValue) {
                        $updatedFields[$requirement->name] = $fieldValue;

                        $resumeField->content = $fieldValue ?? '';
                        $resumeField->save();
                    }
                }
            }
            if (!empty($updatedFields)){
                $userName = Auth::user()->name;
                $positionName = $resume->vacancy->position->name;

                $title = 'Редактирование должности';
                $message = "Сотрудник $userName добавил в '$positionName' новую информацию.";

                $HRs = [];
                $hrPositions = EmployeesPosition::where('department_id', EmployeesDepartment::HR_DEPARTMENT_ID)
                    ->get();
                foreach ($hrPositions as $HR){
                    foreach ($HR->users as $user){
                        $HRs[] = User::find($user->user_id);
                    }
                }
                foreach ($HRs as $HR){
                    if (isset($HR)){
                        Mail::to($HR)->send(new EmployeesEditProfile($title, $message, $updatedFields));
                        Notification::add($HR->id, $message);
                    }
                }
            }
        }
        return redirect()->back();
    }
}