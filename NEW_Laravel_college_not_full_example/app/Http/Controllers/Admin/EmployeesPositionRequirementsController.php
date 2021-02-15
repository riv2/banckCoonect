<?php

namespace App\Http\Controllers\Admin;

use App\{
	EmployeesPosition,
	EmployeesRequirement,
	EmployeesPositionRequirement
};
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Validators\EmployeesPositionRequirements;

class EmployeesPositionRequirementsController extends Controller
{
    public function index(){
        $positions = EmployeesPosition::all();

        return view('admin.pages.employees.positionRequirements', compact('positions'));
    }

    public function getRequirements(Request $request){
        $records = EmployeesRequirement::all();

        return Datatables::of($records)
        	->addColumn('file', function($record){
        		return $record->file? 'Да' : 'Нет';
        	})
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                            <button 
                                class="btn btn-default 
                                editRequirement" 
                                data-requirement-id="'.$record->id.'" 
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="Редактировать требование"
                            >
                                <i class="md md-edit"></i>
                            </button>
                            <button 
                                class="btn btn-default 
                                linkRequirement" 
                                data-requirement-id="'.$record->id.'" 
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="Связать с должностями"
                            >
                                <i class="md md-list"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getPositionRequirement(Request $request){
    	$requirement = EmployeesRequirement::where('id', $request->id)->first();
    	$positions = $requirement->positions;

    	return response()->json(['requirement' => $requirement, 'positions' => $positions]);
    }

    public function addPositionRequirements(Request $request){
        $validator = EmployeesPositionRequirements::make($request->all());
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->getMessageBag()->toArray()], 422);
        }

        $requirement = EmployeesRequirement::create([
        	'name' 		  => $request->name,
        	'description' => $request->description,
        	'start_date'  => $request->start_date,
        	'end_date' 	  => $request->end_date
        ]);

        if(isset($request->file)){
        	$requirement->update([
        		'file' => true
        	]);
        }

        return response()->json(['status' => 'success']);
    }

    public function editPositionRequirements(Request $request){
        $validator = EmployeesPositionRequirements::make($request->all());
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->getMessageBag()->toArray()], 422);
        }

        $data = [
        	'name' 		  => $request->name,
        	'description' => $request->description,
        	'start_date'  => $request->start_date,
        	'end_date' 	  => $request->end_date
        ];

        if(isset($request->file)){
        	$data += ['file' => true];
        }

        $requirement = EmployeesRequirement::where('id', $request->id)->update($data);

        return response()->json(['status' => 'success']);
    }

    public function linkPositionRequirement(Request $request){
    	EmployeesPositionRequirement::where('requirement_id', $request->requirement_id)->delete();

        if(isset($request->positions)){
            foreach ($request->positions as $id) {
                EmployeesPositionRequirement::create([
                    'requirement_id' => $request->requirement_id,
                    'position_id' => $id
                ]);
            }
        }
    	
    	return response()->json(['status' => 'success']);
    }
}
