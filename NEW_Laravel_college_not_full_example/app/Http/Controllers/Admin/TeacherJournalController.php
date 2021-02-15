<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student\StudentDisciplineSemesterRating;
use App\Models\Student\StudentGroupTeacher;
use App\Semester;
use App\StudyGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeacherJournalController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $semesters = Semester::getSemestersList();

        return view('admin.pages.teacherJournal.index', compact('semesters'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTeacherGroups(Request $request)
    {
        $teacherDisciplineStudyGroups = StudentGroupTeacher::where('teacher_id', $request->teacherId)
            ->where('discipline_id', $request->disciplineId)
            ->get();

        $studyGroups = [];

        foreach ($teacherDisciplineStudyGroups as $disciplineStudyGroups){
            foreach ($disciplineStudyGroups->studyGroups as $studyGroup){
                $studyGroups[] = $studyGroup;
            }
        }
        return response()->json($studyGroups);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTypes()
    {
        $teacherTypes = StudentDisciplineSemesterRating::TEACHER_TYPES;
        $allTypes = StudentDisciplineSemesterRating::ALL_TYPES;

        return response()->json(compact('teacherTypes', 'allTypes'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDisciplineGroupStudents(Request $request)
    {
        $data = collect();

        $studyGroup = StudyGroup::find($request->groupId);
        $profiles = $studyGroup->studentsProfiles;

        $allGroupsSemesters = Semester::where('study_form', $profiles->first()->education_study_form)
            ->where('type', 'study')
            ->get();

        $semester = null;
        foreach ($allGroupsSemesters as $groupSemester){
            if ($request->semester == $groupSemester->semesterString){
                $semester = $groupSemester;
            }
        }
        $data->days = StudentDisciplineSemesterRating::getRatingDays($request, $semester, $request->teacherId);
        $data->profiles = $profiles;
        $data->months = $semester->getMonthsWithYearList();

        $semester = Semester::find($semester->id);

        return response()->json([
            'days' => $data->days,
            'profiles' => $data->profiles,
            'months' => $data->months,
            'isEditable' => $semester->isCurent
        ]);
    }
}
