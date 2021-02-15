<?php

namespace App\Http\Controllers\Admin;

use App\{
    EmployeesDepartment,
    Speciality,
    SectorSpeciality
};
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Validators\EmployeesNewDepartment;

class EmployeesController extends Controller
{
	public function __construct(EmployeesDepartment $EmployeesDepartment){
		$this->employeesDepartment = $EmployeesDepartment;
	}

    public function department(){
    	$departments = $this->employeesDepartment->all();
        $specialities = Speciality::all();
        $selectedSpecialities = Speciality::whereHas('sector')->pluck('id')->toArray();

    	return view('admin.pages.employees.department', compact('specialities', 'departments', 'selectedSpecialities'));
    }

    public function departmentDatatable(Request $request)
    {
        $records = EmployeesDepartment::all();

        return Datatables::of($records)
            ->addColumn('superviser', function($record){
                return isset($record->superviser)? EmployeesDepartment::where('id', $record->superviser)->value('name') : '';
            })
            ->addColumn('action', function ($record){
                return '<button class="btn btn-default editDepartment" data-department-id="'.$record->id.'">
                            <i class="md md-edit"></i>
                        </button>
                        <a 
                            href="'.route("delete.department", ["id" => $record->id]).'"  
                            class="btn btn-default" 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="Удалить отдел"
                        >
                            <i class="fa fa-trash"></i>
                        </a>
                        ';
            })
            ->rawColumns(['action', 'superviser'])
            ->make(true);
    }

    public function getDepartment(Request $request){
        $department = $this->employeesDepartment->find($request->id);
        $positions = $department->position()->where('managerial', true)->get();
        $speciality_bc = $department->speciality()->where('speciality_type', 'bc')->pluck('speciality_id')->toArray();
        $speciality_mg = $department->speciality()->where('speciality_type', 'mg')->pluck('speciality_id')->toArray();

        return response()->json([
                            'speciality_bc' => $speciality_bc, 
                            'speciality_mg' => $speciality_mg, 
                            'department' => $department, 
                            'positions' => $positions
                        ]);
    }

    public function addNewDepartment(Request $request){
    	$validator = EmployeesNewDepartment::make($request->all());
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->getMessageBag()->toArray()], 422);
        }

        $department = $this->employeesDepartment->create([
        	'name' => $request->name,
        	'description' => $request->description,
        	'superviser' => $request->superviser?? null,
            'is_sector' => $request->has('checkSpec')? true : false
        ]);

        if($request->has('checkSpec')){
            if($request->has('speciality_bc')){
                foreach ($request->speciality_bc as $key => $value) {
                    SectorSpeciality::create([
                        'department_id'   => $department->id,
                        'speciality_id'   => $value,
                        'speciality_type' => 'bc'
                    ]);
                }
            }
            if($request->has('speciality_mg')){
                foreach ($request->speciality_mg as $key => $value) {
                    SectorSpeciality::create([
                        'department_id'   => $department->id,
                        'speciality_id'   => $value,
                        'speciality_type' => 'mg'
                    ]);
                }
            }
        }
    	
    	return response()->json('success');
    }

    public function editDepartment(Request $request){
        $validator = EmployeesNewDepartment::make($request->all());
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->getMessageBag()->toArray()], 422);
        }

        $array = [
            'name' => $request->name,
            'description' => $request->description,
            'superviser' => $request->superviser,
            'is_sector' => $request->has('checkSpec')? true : false
        ];

        if(isset($request->manager_position_id)){
            $array += [
                'manager_position_id' => $request->manager_position_id
            ];
        }

        $this->employeesDepartment->where('id', $request->id)->update($array);
    	$department = $this->employeesDepartment->where('id', $request->id)->first();
        SectorSpeciality::where('department_id', $department->id)->delete();

        if($request->has('checkSpec')){
            if($request->has('speciality_bc')){
                foreach ($request->speciality_bc as $key => $value) {
                    SectorSpeciality::create([
                        'department_id'   => $department->id,
                        'speciality_id'   => $value,
                        'speciality_type' => 'bc'
                    ]);
                }
            }
            if($request->has('speciality_mg')){
                foreach ($request->speciality_mg as $key => $value) {
                    SectorSpeciality::create([
                        'department_id'   => $department->id,
                        'speciality_id'   => $value,
                        'speciality_type' => 'mg'
                    ]);
                }
            }
        }

    	return response()->json('success');
    }

    public function deleteDepartment($id){
        $department = EmployeesDepartment::where('id', $id)->first();
        $positions  = $department->position;
        foreach ($department->position as $position) {
            $position->vacancy()->delete();
            $position->requirements()->delete();
            $position->delete();
        }
        $department->speciality()->delete();
        $department->delete();
        
        return back();
    }
}
