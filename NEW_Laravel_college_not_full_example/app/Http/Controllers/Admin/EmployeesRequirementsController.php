<?php

namespace App\Http\Controllers\Admin;

use App\{
	EmployeesPosition,
	EmployeesRequirement,
    EmployeesRequirementsField,
	EmployeesPositionRequirement
};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Validators\EmployeesRequirementValidator;

class EmployeesRequirementsController extends Controller
{
    public function index(Request $request, $id){
    	$position 	  		  	 = EmployeesPosition::where('id', $id)->first();
    	$requirements 		  	 = EmployeesRequirement::all();
    	$requirements 		  	 = $requirements->groupBy('category');
    	$positionRequirements 	 = $position->positionRequirements;
    	$positionRequirementsIDs = $positionRequirements->pluck('id')->toArray();

    	return view(
    	    'admin.pages.employees.position_requirements',
            compact('position', 'requirements', 'positionRequirementsIDs')
        );
    }

    public function linkPositionRequirements(Request $request){
    	EmployeesPositionRequirement::where('position_id', $request->position_id)->delete();

    	if (isset($request->requirements)) {
    		foreach ($request->requirements as $value) {
	    		EmployeesPositionRequirement::create([
	    			'position_id' 	 => $request->position_id,
	    			'requirement_id' => $value
	    		]);
	    	}
    	}

    	return redirect()->back()->with('requirements_success_add', 'Требования добавлены успешно');
    }

    public function addNewRequirementPage(Request $request){
        $requirements = EmployeesRequirement::all();
        $requirements = $requirements->groupBy('category');

    	return view('admin.pages.employees.requirements', compact('requirements'));
    }

    public function addNewRequirementField(Request $request){
    	$validator = EmployeesRequirementValidator::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        if($request->category == 'personal_info'){
            $field = EmployeesRequirement::create([
                'name'       => $request->name,
                'category'   => $request->category,
                'field_type' => $request->field_type,
                'field_name' => $request->field_name,
                'options'    => $request->options ?? ''
            ]);
            $fieldId = $field->id;
        } else {
            $field = EmployeesRequirementsField::create([
                'requirement_id' => $request->category,
                'name'           => $request->name,
                'field_name'     => $request->field_name,
                'field_type'     => $request->field_type,
                'options'        => $request->options ?? ''
            ]);
            $fieldId = $field->requirement_id;
        }
        if($request->has('apply_to_all')) {
            $positions = EmployeesPosition::all();
            foreach ($positions as $position){
                EmployeesPositionRequirement::create([
                    'position_id' 	 => $position->id,
                    'requirement_id' => $fieldId
                ]);
            }
        }
        return redirect()->back()->with('requirement_success_add', 'Требование добавлено успешно');
    }

    public function addNewRequirement(Request $request){
        EmployeesRequirement::create([
            'name'       => $request->name_requirement,
            'category'   => $request->category_name,
            'field_type' => 'json',
            'field_name' => 'json',
            'options'    => null
        ]);

        return redirect()->back()->with('requirement_success_add', 'Требование добавлено успешно');
    }
}
