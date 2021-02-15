<?php

namespace App\Http\Controllers\Student;

use App\Info;
use App\Http\Controllers\Controller;

class InformationController extends Controller
{
    public function show($info_type) {
        $is_important = $info_type == 'important';

        $info = Info::where('is_important', $is_important)->orderByDesc('created_at')->paginate(3);

        return view('student.info.list', compact(
            'info',
            'info_type'
        ));
    }

    public function detailsShow($info_id) {
        $info = Info::find($info_id);

        if (empty($info)) {
            abort(404);
        }

        return view('student.info.details', compact('info'));
    }
}
