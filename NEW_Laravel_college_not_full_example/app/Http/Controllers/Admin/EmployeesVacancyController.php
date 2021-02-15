<?php

namespace App\Http\Controllers\Admin;

use App\{
    EmployeesPosition,
    EmployeesVacancy,
    ManualWorkShedule
};
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Validators\EmployeesVacancyValidation;
use App\Http\Controllers\Controller;

class EmployeesVacancyController extends Controller
{
    public function index(){
    	$positions = EmployeesPosition::all();
        $work_schedule = ManualWorkShedule::all();

    	return view('admin.pages.employees.vacancy', compact('positions', 'work_schedule'));
    }

    public function vacancyDatatable(Request $request){
        $records = EmployeesVacancy::all();

        return Datatables::of($records)
            ->addColumn('name', function ($record){
                return EmployeesPosition::where('id', $record->position_id)->value('name');
            })
            ->addColumn('description', function ($record){
                return EmployeesPosition::where('id', $record->position_id)->value('description');
            })
            ->addColumn('department', function ($record){
                return isset($record->position) 
                        ? isset($record->position->department)
                            ? $record->position->department->name 
                            : 'Отдел удалён'
                        : 'Должность удалена';
            })
            ->addColumn('schedule', function ($record){
                return ManualWorkShedule::where('id', $record->schedule_id)->value('name');
            })
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                            <a href="'.route("employees.delete.vacantion", ["id" => $record->id]).'">
                                <button class="btn btn-default"><i class="fa fa-trash"></i></button>
                            </a>
                        </div>';
            })
            ->rawColumns(['action', 'name', 'description', 'department'])
            ->make(true);
    }

    public function deleteVacancy($id){
        EmployeesVacancy::where('id', $id)->delete();

        return back();
    }

    public function addVacancy(Request $request){
    	$validator = EmployeesVacancyValidation::make($request->all());
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->getMessageBag()->toArray()], 422);
        }

        EmployeesVacancy::create([
        	'position_id' => $request->position_id,
            'schedule_id' => $request->schedule_id,
            'employment'  => $request->employment,
            'price'       => $request->price,
            'salary'      => $request->salary
        ]);
    	
    	return response()->json(['status' => 'success']);
    }
}
