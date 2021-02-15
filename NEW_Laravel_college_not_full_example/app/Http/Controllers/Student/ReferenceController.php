<?php

namespace App\Http\Controllers\Student;


use App\Http\Controllers\Controller;
use App\FinanceNomenclature;
use App\Language;
use App\StudentFinanceNomenclature;
use Illuminate\Http\Request;
use Auth;

class ReferenceController extends Controller
{
    public function index()
    {
        $references = FinanceNomenclature::getReferences();
        $locale = Language::getFieldName('name', app()->getLocale());
        $boughtServiceIds = StudentFinanceNomenclature::getBoughtServiceIds(Auth::user()->id, Auth::user()->studentProfile->currentSemester());

        return view('student.references', compact(
            'locale',
            'references',
            'boughtServiceIds'
        ));
    }
}
