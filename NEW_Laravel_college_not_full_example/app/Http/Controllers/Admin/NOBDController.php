<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Services\NOBDApi;
use Illuminate\Http\Request;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Validators\NOBDValidation;

class NOBDController extends Controller
{
    public function index(){
    	return view('admin.pages.nobd.index');
    }

    public function downloadList(Request $request)
    {
        $year = $this->request['year']?? date("Y");

        $students = User::query()
                    ->whereHas('studentProfile', function ($query) {
                        $query->where('education_status', '=', 'student')
                                ->orWhere('education_status', '=', 'pregraduate');
                    })
                    ->where(function ($query) use ($year) {
                        $query->whereHas('bcApplication')
                              ->orWhereHas('mgApplication');
                    })
                    ->whereYear('created_at', '=', $year)
                    ->get()->toArray();
       
        return Excel::create( 'Students' . ($year ? ('_' . $year) : ''), function($excel) use ($students, $year) {
            $excel->sheet('List 1', function($sheet) use ($students)
            {
                $sheet->fromArray($students);
            });
        })->download('xls');
    }

    public function uploadList(Request $request)
    {
        $validator = NOBDValidation::make($request->all());
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->getMessageBag()->toArray()], 422);
        }

        $iin = '';
    	$toNOBD = [];
        $students = Excel::load($request->file('students'));
        $students = $students->toArray();

        foreach ($students as $key => $value) {
            foreach($value as $k => $val){
                if ($k !== 'iin' && $val !== null) {
                    if(in_array($k, config('nobd.group_logic_values'))){
                        $toNOBD[$key]['updatedValues'][] = ['code' => $k, 'values' => [['value' => [strval($val)]]]];
                    }
                    elseif(count(explode(',', $val)) > 1){
                        $toNOBD[$key]['updatedValues'][] = ['code' => $k, 'values' => [['value' => explode(',', strval($val))]]];
                    }else{
                        if ($k == 6663){
                            $toNOBD[$key]['updatedValues'][] = ['code' => $k, 'values' => [['value' => intval($val)]]];
                        } else {
                            $toNOBD[$key]['updatedValues'][] = ['code' => $k, 'values' => [['value' => strval($val)]]];
                        }
                    }
                }
            }
            if($value['iin'] !== null){
                $toNOBD[$key]['deletedValues'] = [];
                $toNOBD[$key]['bin'] = config('nobd.bin');
                $toNOBD[$key]['typeCode'] = config('nobd.typeCode');
                $toNOBD[$key]['iin'] = strval($value['iin']);
                $toNOBD[$key]['status'] = config('nobd.status');
                $toNOBD[$key]['importSource'] = config('nobd.importSource');
            }
        }

        $api = new NOBDApi();
        $result = $api->importStudent($toNOBD, $request->login, $request->password, $request->url);

        return response()->json($result);
    }
}
