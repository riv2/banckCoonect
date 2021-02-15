<?php

namespace App\Http\Controllers\Admin;

use App\{
	ManualWorkShedule,
	ManualNationality,
	ManualCitizenship,
    ManualIssuingDocs,
    ManualEducation,
    ManualPerk,
    ManualOrganization
};
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Validators\{
	ManualSheduleValidation,
	ManualNationalityValidation,
    ManualEducationValidation,
    ManualPerksValidation,
    ManualOrganizationsValidation
};

class ManualController extends Controller
{
    public function index(){
    	return view('admin.pages.manual.index');
    }

    public function sheduleDatatable(){
    	$records = ManualWorkShedule::all();

        return Datatables::of($records)
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                        	<a href="'.route("admin.manual.delete.note.shedule", ["id" => $record->id]).'"><button class="btn btn-default"><i class="fa fa-trash"></i></button></a>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addNotePage($name){
    	return view('admin.pages.manual.manuals_list.'.$name.'');
    }

    public function addNoteShedule(Request $request){
    	$validator = ManualSheduleValidation::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

    	ManualWorkShedule::create([
    		'name' => $request->name,
    		'description' => $request->description
    	]);

    	return redirect()->back()->with('manual_success_add', 'Запись добавлена успешно');
    }

    public function deleteNoteShedule($id){
        ManualWorkShedule::where('id', $id)->delete();

        return back();
    }

    public function nationalityDatatable(){
    	$records = ManualNationality::all();

        return Datatables::of($records)
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                        	<a href="'.route("admin.manual.delete.note.nationality", ["id" => $record->id]).'"><button class="btn btn-default"><i class="fa fa-trash"></i></button></a>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addNoteNationality(Request $request){
    	$validator = ManualNationalityValidation::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

    	ManualNationality::create([
    		'name' => $request->name,
    		'link' => $request->link
    	]);

    	return redirect()->back()->with('manual_success_add', 'Запись добавлена успешно');
    }

    public function deleteNoteNationality($id){
        ManualNationality::where('id', $id)->delete();

        return back();
    }

    public function citizenshipDatatable(){
    	$records = ManualCitizenship::all();

        return Datatables::of($records)
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                        	<a href="'.route("admin.manual.delete.note.citizenship", ["id" => $record->id]).'"><button class="btn btn-default"><i class="fa fa-trash"></i></button></a>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addNoteCitizenship(Request $request){
    	$validator = ManualNationalityValidation::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

    	ManualCitizenship::create([
    		'name' => $request->name,
    		'link' => $request->link
    	]);

    	return redirect()->back()->with('manual_success_add', 'Запись добавлена успешно');
    }

    public function deleteNoteCitizenship($id){
        ManualCitizenship::where('id', $id)->delete();

        return back();
    }

    public function issuingDocsDatatable(){
        $records = ManualIssuingDocs::all();

        return Datatables::of($records)
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                            <a href="'.route("admin.manual.delete.note.issuing.docs", ["id" => $record->id]).'"><button class="btn btn-default"><i class="fa fa-trash"></i></button></a>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addNoteIssuingDocs(Request $request){
        $validator = ManualNationalityValidation::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        ManualIssuingDocs::create([
            'name' => $request->name,
            'link' => $request->link
        ]);

        return redirect()->back()->with('manual_success_add', 'Запись добавлена успешно');
    }

    public function deleteNoteIssuingDocs($id){
        ManualIssuingDocs::where('id', $id)->delete();

        return back();
    }

    public function educationDatatable(Request $request){
        $records = ManualEducation::all();

        return Datatables::of($records)
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                            <a href="'.route("admin.manual.delete.note.education", ["id" => $record->id]).'"><button class="btn btn-default"><i class="fa fa-trash"></i></button></a>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addNoteEducation(Request $request){
        $validator = ManualEducationValidation::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        ManualEducation::create([
            'name'          => $request->name,
            'name_en'       => $request->name_en,
            'name_kz'       => $request->name_kz,
            'short_name'    => $request->short_name,
            'short_name_en' => $request->short_name_en,
            'short_name_kz' => $request->short_name_kz,
            'type'          => $request->type
        ]);

        return redirect()->back()->with('manual_success_add', 'Запись добавлена успешно');
    }

    public function deleteNoteEducation($id){
        ManualEducation::where('id', $id)->delete();

        return back();
    }

    public function perksDatatable(Request $request){
        $records = ManualPerk::all();

        return Datatables::of($records)
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                            <a href="'.route("admin.manual.delete.note.perks", ["id" => $record->id]).'"><button class="btn btn-default"><i class="fa fa-trash"></i></button></a>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addNotePerks(Request $request){
        $validator = ManualPerksValidation::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        ManualPerk::create([
            'name'  => $request->name,
            'value' => $request->value
        ]);

        return redirect()->back()->with('manual_success_add', 'Запись добавлена успешно');
    }

    public function deleteNotePerks($id){
        ManualPerk::where('id', $id)->delete();

        return back();
    }

    public function organizationsDatatable(Request $request){
        $records = ManualOrganization::all();

        return Datatables::of($records)
            ->addColumn('action', function ($record){
                return '<div class="text-center">
                            <a href="'.route("admin.manual.delete.note.organizations", ["id" => $record->id]).'"><button class="btn btn-default"><i class="fa fa-trash"></i></button></a>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addNoteOrganizations(Request $request){
        $validator = ManualOrganizationsValidation::make($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        ManualOrganization::create([
            'name'  => $request->name
        ]);

        return redirect()->back()->with('manual_success_add', 'Запись добавлена успешно');
    }

    public function deleteNoteOrganizations($id){
        ManualOrganization::where('id', $id)->delete();

        return back();
    }
}
