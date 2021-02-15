<?php

namespace App\Http\Controllers\Student;

use App\Discipline;
use App\Services\Auth;
use App\Services\LanguageService;
use App\SpecialityDiscipline;
use App\SpecialitySubmodule;
use App\StudentDiscipline;
use App\Syllabus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SyllabusController extends Controller
{
    /**
     * @param int $disciplineId
     * @param null $lang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getList(int $disciplineId, $lang = null)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        $SD->setSyllabusButtonShow(Auth::user());
        if (!$SD->syllabusButtonShow || empty(Auth::user()->studentProfile) ) {
            abort(404);
        }

        StudentDiscipline
            ::where('discipline_id', $disciplineId)
            ->where('student_id', Auth::user()->id)
            ->update(['syllabus_updated' => 0]);

        $discipline = Discipline::where('id', $disciplineId)->first();

        $specialityDiscipline = SpecialityDiscipline::getOne(Auth::user()->studentProfile->education_speciality_id, $disciplineId);
        if (!$specialityDiscipline) {
            $specialityDiscipline = SpecialitySubmodule::getByDisciplineId(Auth::user()->studentProfile->education_speciality_id, $disciplineId);
        }

        // For elective disciplines
        if (!$specialityDiscipline && !empty(Auth::user()->studentProfile->elective_speciality_id)) {
            $specialityDiscipline = SpecialityDiscipline::getOne(Auth::user()->studentProfile->elective_speciality_id, $disciplineId);
            if (!$specialityDiscipline) {
                $specialityDiscipline = SpecialitySubmodule::getByDisciplineId(Auth::user()->studentProfile->elective_speciality_id, $disciplineId);
            }
        }

        if (!$specialityDiscipline) {
            abort(404);
        }

        $syllabusLang = in_array($lang, ['ru', 'en', 'kz']) ? $lang : null;

        if (!$syllabusLang) {
            $syllabusLang = LanguageService::getByType(
                $specialityDiscipline->language_type,
                Auth::user()->studentProfile->education_lang);
        }

        $syllabusList = Syllabus
            ::with('teoreticalMaterials')
            ->with('practicalMaterials')
            ->with('sroMaterials')
            ->with('sropMaterials')
            ->with('module')
            ->where('discipline_id', $disciplineId)
            ->where('language', $syllabusLang)
            ->orderBy('module_id')
            ->get()
            ->groupBy('module.name');

        return view('student.syllabus', [
            'syllabusList' => $syllabusList,
            'discipline' => $discipline,
            'syllabusLang' => $syllabusLang,
            'syllabusLangList' => $discipline->themeLangs()
        ]);
    }
}
