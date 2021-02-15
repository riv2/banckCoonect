<?php

namespace App\Http\Controllers\Admin;

use App\{
	EmployeesUsersDecree
};
use DOMDocument;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\Shared\ZipArchive;

class EmployeesDecreeController extends Controller
{
    //////////////////////////////////// TO DO ////////////////////////////////////////
    public function usersDecreePage(){
    	return view('admin.pages.employees.all_decree');
    }

    public function usersDecreeDatatable(Request $request){
        $records = EmployeesUsersDecree::all();

        return Datatables::of($records)
        	->addColumn('user_name', function ($record){
        		return $record->user->name;
        	})
        	->addColumn('decree', function ($record){
        		return str_limit($record->name, 30);
        	})
            ->addColumn('created_at', function ($record){
                return $record->created_at->format('Y-m-d H:i');
            })
            ->addColumn('is_signed', function($record){
                return $record->is_signed == false ? 'Без подписи' : 'Подписанный';
            })
            ->addColumn('action', function ($record){
                return '<a 
                            href="'.route('employees.decree.edit', ["id" => $record->id]).'" 
                            class="btn btn-default" 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="Редактировать приказ"
                        >
                            <i class="md md-edit"></i>
                        </a>';
            })
            ->rawColumns(['action', 'user_name'])
            ->make(true);
    }

    public function downloadDecree($name){
        $file = storage_path('app/employees/users/decree/').$name;

        return response()->download($file);
    }

    public function uploadDecree(Request $request){
        $decree = EmployeesUsersDecree::where('id', $request->decree_id)->first();
        $fileName = 'SIGNED_'.$decree->name;
        $request->decree->move(storage_path('app/employees/users/decree/'), $fileName);

        $decree->update([
            'name'      => $fileName,
            'is_signed' => 1
        ]);

        return redirect()->route('employees.decree.all');
    }

    public function editDecreePage($id){
    	$decree = EmployeesUsersDecree::where('id', $id)->first();
    	$text = '';
    	$filePath = storage_path('app/employees/users/decree/').$decree->name;

    	// Create new ZIP archive
	    $zip = new ZipArchive;
	    $dataFile = 'word/document.xml';
	    // Open received archive file
	    if (true === $zip->open($filePath)) {
	        // If done, search for the data file in the archive
	        if (($index = $zip->locateName($dataFile)) !== false) {
	            // If found, read it to the string
	            $data = $zip->getFromIndex($index);
	            // Close archive file
	            $zip->close();
	            // Load XML from a string
	            // Skip errors and warnings
	            $d = new DOMDocument();
	            $xml = $d->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
	            // Return data without XML formatting tags
	            $contents = explode('\n',strip_tags($d->saveXML()));

	            foreach($contents as $i=>$content) {
	                $text .= $contents[$i];
	            }

	        } else {
	        	$zip->close();
	        }
	    }

    	return view('admin.pages.employees.edit_decree', compact('decree', 'text'));
    }
}
