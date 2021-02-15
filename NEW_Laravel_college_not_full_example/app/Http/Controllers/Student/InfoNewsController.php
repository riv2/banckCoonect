<?php

namespace App\Http\Controllers\Student;

use App\InfoNews;
use App\Http\Controllers\Controller;

class InfoNewsController extends Controller
{
    public function show($info_type) {
        $is_important = $info_type == 'important';

        $info = InfoNews::where('is_important', $is_important)->orderByDesc('created_at')->paginate(3);

        return view('student.news.list', compact(
            'info',
            'info_type'
        ));
    }

    public function detailsShow($info_id) {
        $info = InfoNews::find($info_id);

        if (empty($info)) {
            abort(404);
        }

        return view('student.news.details', compact('info'));
    }
}
