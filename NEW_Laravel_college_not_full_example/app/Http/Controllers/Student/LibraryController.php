<?php

namespace App\Http\Controllers\Student;

use Auth;
use Mail;
use App\{
    User,
    LibraryReport,
    LibraryVisitStatistic,
    LibraryLiteratureCatalog
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Services\LibrarySaveFile;
use App\Http\Controllers\Controller;


class LibraryController extends Controller
{
    public function index(Request $request, $search = null){
        $literatures = null;

        if(isset($request['search']) && $request['search'] != ''){
            $literatures = LibraryLiteratureCatalog::where('name', 'like', '%'.$request['search'].'%')->paginate(10);
        } else {
            $literatures = LibraryLiteratureCatalog::paginate(10);
        }

        $check = LibraryVisitStatistic::where('user_id', Auth::user()->id)
                                        ->where('created_at', '>=', Carbon::now()->subMinutes(10)->toDateTimeString())
                                        ->first();

        if(empty($check)){
            LibraryVisitStatistic::create([
                'user_id' => Auth::user()->id
            ]);
        }

    	return view('student.library.index', compact('literatures'));
    }

    public function showLiteraturePage($id){
    	$literature = LibraryLiteratureCatalog::find($id);

    	return view('student.library.literature', compact('literature'));
    }

    public function downloadFile($fileName, $ID){
        $file = new LibrarySaveFile();

        $path = $file->getFile($fileName);

        LibraryReport::create([
            'user_id'       => Auth::user()->id,
            'literature_id' => $ID,
            'action_type'   => 'download',
            'status'        => 'download'
        ]);

        return response()->download($path);
    }

    public function literatureOrder(Request $reqeust){
        
        LibraryReport::create([
            'user_id'       => Auth::user()->id,
            'literature_id' => $reqeust['literature_id'],
            'action_type'   => 'order',
            'status'        => 'order'
        ]);

        $literature = LibraryLiteratureCatalog::find($reqeust['literature_id']);
        $usersEmail = User::whereHas('positions', function ($query) {
            $query->whereHas('position', function ($query){
                $query->where('name', 'like', '%Библиотекарь%');
            });
        })->pluck('email')->toArray();

        foreach($usersEmail as $key => $value){
            if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                unset($usersEmail[$key]); 
            }
        }

        Mail::send('emails.library.literature_order',
            [ 'literature' => $literature ], 
            function ($message) use ($usersEmail) {
                $message->from(getcong('site_email'), getcong('site_name'));
                $message->to( $usersEmail )->subject('Новая заявка на литературу');
            });

        return response()->json(['status' => 'success']);
    }
}
