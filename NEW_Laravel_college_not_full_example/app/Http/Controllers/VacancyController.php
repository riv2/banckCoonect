<?php

namespace App\Http\Controllers;

use Auth;
use App\{
	EmployeesUser,
    EmployeesPosition,
    EmployeesVacancy,
    ManualWorkShedule,
    EmployeesUsersResume,
    ManualCitizenship,
    EmployeesUserRequirement,
    ManualNationality
};
use App\Jobs\SendEmployeesCandidateEmail;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Validators\{
    UserVacancyFormValidation
};

class VacancyController extends Controller
{
    public function index(){
    	return view('pages.vacancy.index');
    }

    public function vacancyDatatable(Request $request){
    	$userPositions = Auth::user()->positions->pluck('position_id');
    	$userResume = EmployeesUsersResume::where('user_id', Auth::user()->id)->pluck('vacancy_id');
    	$records = EmployeesVacancy::whereNotIn('position_id', $userPositions->toArray())->whereNotIn('id', $userResume->toArray());

        return Datatables::of($records)
            ->addColumn('name', function ($record){
                return EmployeesPosition::where('id', $record->position_id)->value('name');
            })
            ->addColumn('description', function ($record){
                return EmployeesPosition::where('id', $record->position_id)->value('description');
            })
            ->addColumn('schedule_id', function ($record){
                return ManualWorkShedule::where('id', $record->schedule_id)->value('name');
            })
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                            <a href="'.route("get.vacancy.form", ["id" => $record->id]).'"><button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Заполнить анкету"><i class="fas fa-edit"></i></button>
                        </div>';
            })
            ->rawColumns(['action', 'name', 'description', 'department'])
            ->make(true);
    }

    public function getVacancyForm($id){
    	$user = Auth::user();
    	$vacancy = EmployeesVacancy::where('id', $id)->first();
        $requirements = $vacancy->position->getRequirementsArray();
        $citizenships = ManualCitizenship::all();
        $nationalities = ManualNationality::all();

        $userPositions = $user->positions->pluck('position_id');
        $userResume = EmployeesUsersResume::where('user_id', $user->id)->pluck('vacancy_id');

        if(in_array($vacancy->position_id, $userPositions->toArray()) || in_array($vacancy->id, $userResume->toArray())){
            return redirect()->route('vacancy.index');
        }

    	return view('pages.vacancy.form', compact('vacancy', 'requirements', 'citizenships', 'nationalities'));
    }

    public function submitForm(Request $request){
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
                    $aRuleList += ['requirements.'.$value['category'].'.*.'.$value['field_name'] => 'nullable|'.$field.$rule];
                }
            } else {
                foreach ($value['fields'] as $val) {
                    $rule = $val['field_type'] == 'text' || $val['field_type'] == 'date' || $val['field_type'] == 'select' ? '|max:255' : '|max:10240';
                    $field = $val['field_type'] == 'text' ? 'string' : $val['field_type'];
                    if($val['field_name'] != 'json'){
                        $aRuleList += ['requirements.'.$value['category'].'.*.'.$val['field_name'] => 'nullable|'.$field.$rule];
                    }
                }
            }
            
        }

    	$validator = UserVacancyFormValidation::make($request->all(), $aRuleList, [], $aMessageList);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        /*** Check if user already exist with this vacancy ***/
        $user = Auth::user();

        if(null != EmployeesUsersResume::where('user_id', $user->id)->where('vacancy_id', $request->vacancy_id)->first()){
            return redirect()->route('vacancy.resume');
        }

        /*** Create resume, requirements and employeesUser ***/
        $resume = EmployeesUsersResume::create([
            'user_id'    => $user->id,
            'vacancy_id' => $request->vacancy_id,
            'status'     => 'pending'
        ]);

        $posRequirements = $vacancy->position->getRequirementsArray();

        foreach ($posRequirements as $category => $category_value) {
            if ($category == 'personal_info') {
                foreach (reset($category_value) as $pos_key => $pos_field_content) {
                    foreach ($request->requirements[$category] as $requirementID => $field_content) {
                        if(
                            $pos_field_content['field_type'] == 'file' && 
                            !array_key_exists($pos_field_content['id'], $request->requirements[$category])
                        )
                        {
                            EmployeesUserRequirement::create([
                                'resume_id'      => $resume->id,
                                'requirement_id' => $pos_field_content['id'],
                                'content'        => ''
                            ]);
                        } else 
                        {
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
                    }
                }
            } else {
                foreach ($category_value as $requirement_name => $requirement_value) {
                    $json = [];
                    foreach ($requirement_value[array_key_first($requirement_value)]['fields'] as $pos_requirement) {
                        foreach($request->requirements[$category][$requirement_value[array_key_first($requirement_value)]['id']] as 
                            $requirement_index => $requirement_fields){
                            foreach ($requirement_fields as $requirement_field_name => $requirement_field_content) {
                                if($pos_requirement['field_type'] == 'file' && !array_key_exists($pos_requirement['field_name'], $requirement_fields)){
                                    $json[$requirement_index][$pos_requirement['field_name']] = '';
                                } else {
                                    if($pos_requirement['field_type'] == 'file' && gettype($requirement_field_content) == 'object'){
                                        $fileName = time().'_'.$requirement_field_content->getClientOriginalName();
                                        $requirement_field_content->move(storage_path('app/employees/users/requirements/'), $fileName);
                                        $json[$requirement_index][$requirement_field_name] = $fileName;
                                    } else {
                                        $json[$requirement_index][$requirement_field_name] = $requirement_field_content;
                                    }
                                }
                            }
                        }
                    }
                    EmployeesUserRequirement::create([
                        'resume_id'      => $resume->id,
                        'requirement_id' => $pos_requirement['requirement_id'],
                        'content'        => 'json_content',
                        'json_content'   => json_encode($json)
                    ]);
                }
            }
        }

        $employee = EmployeesUser::where('user_id', $user->id)->first();

        if(empty($employee)){
            EmployeesUser::create([
                'user_id' => $user->id,
                'status' => 'кандидат'
            ]);
        }

        /*** Send email to oauk admins ***/
        $resumeID = $resume->id;
        SendEmployeesCandidateEmail::dispatch($resumeID);

    	return redirect()->route('vacancy.index');
    }

    public function resumePage(){
        return view('pages.vacancy.resume');
    }

    public function vacancyResumeDatatable(Request $request){
        $records = EmployeesUsersResume::where('user_id', Auth::user()->id)->get();

        return Datatables::of($records)
            ->addColumn('vacancy', function ($record){
                return isset($record->vacancy) 
                        ? isset($record->vacancy->position) 
                            ? $record->vacancy->position->name 
                            : 'Должность удалена'
                        : 'Вакансия удалена';
            })
            ->addColumn('status', function ($record){
                $statuses = EmployeesUsersResume::$statuses;

                return $statuses[$record->status];
            })
            ->addColumn('updated_at', function ($record){
                return $record->updated_at->format('Y-m-d');
            })
            ->addColumn('action', function ($record){
                if(isset($record->vacancy) && isset($record->vacancy->position)){
                    switch ($record->status) {
                        case 'need_requirements':
                            return '<div class="text-center">
                                        <a href="'.route("get.position.requirements.form", ["id" => $record->vacancy_id]).'"><button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Заполнить анкету"><i class="fas fa-edit"></i></button></a>
                                    </div>';
                            break;

                        case 'revision_position_requirements':
                            return '<div class="text-center">
                                        <a href="'.route("revision.resume.type.id", ["id" => $record->id]).'"><button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Редактировать"><i class="fas fa-edit"></i></button></a>
                                    </div>';
                            break;

                        case 'approved':
                            return '<div class="text-center">
                                        <a href="'.route("revision.resume.type.id", ["id" => $record->id, "type" => "edit"]).'">
                                            <button 
                                                class="btn btn-default" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Редактировать требования"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </a>
                                    </div>';
                            break;

                        default:
                            return '';
                            break;
                    }
                } else {
                    return '';
                }
                
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function revisionPage($id, $type = null){
        $resume = EmployeesUsersResume::where('id', $id)->first();
        $requirements = $resume->userRequirementsWithOriginal();
        $citizenships = ManualCitizenship::all();
        $nationalities = ManualNationality::all();

        return view('pages.vacancy.revision', compact('resume', 'requirements', 'type', 'citizenships', 'nationalities'));
    }

    public function revisionSubmit(Request $request){
        $resume = EmployeesUsersResume::where('id', $request->resume_id)->first();
        $posRequirements = $resume->vacancy->position->getRequirementsArray();

        foreach ($posRequirements as $category => $category_value) {
            if ($category == 'personal_info') {
                foreach (reset($category_value) as $pos_key => $pos_field_content) {
                    foreach ($request->requirements[$category] as $requirementID => $field_content) {
                        if(
                            $pos_field_content['field_type'] == 'file' && 
                            !array_key_exists($pos_field_content['id'], $request->requirements[$category])
                        )
                        {
                            EmployeesUserRequirement::where('resume_id', $request->resume_id)->where('requirement_id', $requirementID)->update([
                                'content' => ''
                            ]);
                        } else 
                        {
                            if(gettype($field_content) == 'object'){
                                $fileName = time().'_'.$field_content->getClientOriginalName();
                                $field_content->move(storage_path('app/employees/users/requirements/'), $fileName);
                                $field_content = $fileName;
                            }
                            EmployeesUserRequirement::where('resume_id', $request->resume_id)->where('requirement_id', $requirementID)->update([
                                'content' => $field_content?? ''
                            ]);
                        }
                    }
                }
            } else {
                foreach ($category_value as $requirement_name => $requirement_value) {
                    $json = [];
                    foreach ($requirement_value[array_key_first($requirement_value)]['fields'] as $pos_requirement) {
                        foreach(
                            $request->requirements[$category][$requirement_value[array_key_first($requirement_value)]['id']] as 
                            $requirement_index => $requirement_fields
                        ){
                            foreach ($requirement_fields as $requirement_field_name => $requirement_field_content) {
                                if($pos_requirement['field_type'] == 'file' && !array_key_exists($pos_requirement['field_name'], $requirement_fields)){
                                    $json[$requirement_index][$pos_requirement['field_name']] = '';
                                } else {
                                    if($pos_requirement['field_type'] == 'file' && gettype($requirement_field_content) == 'object'){
                                        $fileName = time().'_'.$requirement_field_content->getClientOriginalName();
                                        $requirement_field_content->move(storage_path('app/employees/users/requirements/'), $fileName);
                                        $json[$requirement_index][$requirement_field_name] = $fileName;
                                    } else {
                                        $json[$requirement_index][$requirement_field_name] = $requirement_field_content;
                                    }
                                }
                            }
                        }
                    }
                    $requirement = EmployeesUserRequirement::where('resume_id', $request->resume_id)
                                                            ->where('requirement_id', $pos_requirement['requirement_id'])
                                                            ->first();
                    $requirement->json_content = json_encode($json);
                    $requirement->save();
                }
            }
        }

        $resume->update(['status' => 'pending']);

        return redirect()->route('vacancy.resume');
    }
}
