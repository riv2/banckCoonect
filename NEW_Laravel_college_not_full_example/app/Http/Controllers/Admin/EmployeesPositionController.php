<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\EmployeesPosition;
use App\EmployeesDepartment;
use App\EmployeesPositionRole;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Validators\EmployeesNewPosition;

class EmployeesPositionController extends Controller
{

    public function index(){
    	$departments = EmployeesDepartment::all();
        $roles = Role::all();

    	return view('admin.pages.employees.position', compact('departments', 'roles'));
    }

    public function positionDatatable(Request $request)
    {
    	$records = EmployeesPosition::all();

        return Datatables::of($records)
            ->addColumn('department_id', function($record){
                return isset($record->department) ? $record->department->name : 'Отдел удалён';
            })
            ->addColumn('action', function ($record){
                return '
                    <div class="row">
                            <button 
                                class="btn btn-default editPosition" 
                                data-position-id="'.$record->id.'" 
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="Редактировать должность"
                            >
                                <i class="md md-edit"></i>
                            </button>
                            <a 
                                href="'.route("position.requirements.page", ["id" => $record->id]).'"
                                class="btn btn-default" 
                                data-position-id="'.$record->id.'" 
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="Добавить требования"
                            >
                                <i class="md md-list"></i>
                            </a>
                            <a 
                                href="'.route("delete.position", ["id" => $record->id]).'"  
                                class="btn btn-default" 
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="Удалить должность"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                    </div>';
            })
            ->rawColumns(['action', 'department_id'])
            ->make(true);
    }

    public function getPosition(Request $request){
    	$position = EmployeesPosition::find($request->id);
        $roles_ids = $position->roles()->pluck('roles.id')->toArray();
        $position->roles_ids = $roles_ids;

        return response()->json($position);
    }

    public function addNewPosition(Request $request){
    	$validator = EmployeesNewPosition::make($request->all());
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->getMessageBag()->toArray()], 422);
        }

        $position = EmployeesPosition::create([
        	'name' => $request->name,
        	'description' => $request->description,
        	'department_id' => $request->department_id
        ]);

        if($request->has('managerPosition')){
            $position->update([
                'managerial' => true
            ]);
        }

        if($request->has('roles')){
            foreach ($request->roles as $role){
                EmployeesPositionRole::create([
                    'position_id' => $position->id,
                    'role_id'     => $role
                ]);
            }
        }
    	
    	return response()->json(['status' => 'success']);
    }

    public function editPosition(Request $request){
        $validator = EmployeesNewPosition::make($request->all());
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->getMessageBag()->toArray()], 422);
        }

        $manag = false;

        if($request->has('managerPosition')){
            $manag = true;
        }

    	EmployeesPosition::where('id', $request->id)->update([
    		'name' => $request->name,
    		'description' => $request->description,
        	'department_id' => $request->department_id,
            'managerial' => $manag
    	]);

        EmployeesPositionRole::where('position_id', $request->id)->delete();
        if($request->has('roles')){
            foreach ($request->roles as $role){
                EmployeesPositionRole::create([
                    'position_id' => $request->id,
                    'role_id'     => $role
                ]);
            }
        }

    	return response()->json(['status' => 'success']);
    }

    public function deletePosition($id){
        $position = EmployeesPosition::where('id', $id)->first();
        $position->vacancy()->delete();
        $position->requirements()->delete();
        $position->delete();

        return back();
    }
}
