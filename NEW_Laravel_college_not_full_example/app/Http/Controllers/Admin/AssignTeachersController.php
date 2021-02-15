<?php

namespace App\Http\Controllers\Admin;

use App\AdminUserDiscipline;
use App\Discipline;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudyGroups\AssignTeacherRequest;
use App\Http\Requests\StudyGroups\GroupingStudyGroupsRequest;
use App\Models\Student\StudentGroupTeacher;
use App\Profiles;
use App\Semester;
use App\SpecialityDiscipline;
use App\StudentGroupsSemesters;
use DataTables;
use DB;
use Illuminate\Http\Request;

class AssignTeachersController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $semesters = Semester::getSemestersList();

        return view('admin.pages.assignTeachers.index', compact('semesters'));
    }

    /**
     * @param string $semester
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDisciplinesBySemester(string $semester)
    {
        $disciplines = Discipline::hasSpecialitySemester($semester)->get();

        return response()->json($disciplines);
    }

    /**
     * @param $disciplineId
     * @return mixed
     * @throws \Exception
     */
    public function getDisciplineGroups($disciplineId)
    {
        $discipline = Discipline::find($disciplineId);


        return DataTables::of($discipline->getStudyGroupsForAssignTeachers())
            ->addColumn('group', function ($groups){
                $html = '';
                foreach ($groups['groups'] as $group){
                    $name = $group['name'];
                    $html .= "<div>$name</div>";
                }
                return $html;
            })->addColumn('teacher', function ($groups) use ($discipline){
                $groupId = isset($groups['groups'][0]) ? $groups['groups'][0]['id']: null;
                $html = "<select class='form-control' onchange='app.changeGroupTeacher($groupId, this)'><option value=''></option>";

                foreach ($discipline->teachers as $teacher) {
                    $checked = $groups['teacher'] === $teacher->id ? 'selected': '';
                    $html .= "<option $checked value='$teacher->id'>$teacher->name</option>";
                }
                return $html.'</select>';
            })->addColumn('actions', function ($groups){
                $groupId = isset($groups['groups'][0]) ? $groups['groups'][0]['id']: null;
                $html = "<input type='checkbox' onchange='app.addToGrouping($groupId, this)'/>";

                return $html;
            })
            ->rawColumns(['group', 'teacher', 'actions'])
            ->toJson();
    }

    /**
     * @param $disciplineId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDisciplineTeachers($disciplineId)
    {
        $discipline = Discipline::find($disciplineId);

        return response()->json($discipline->teachers);
    }

    /**
     * @param AssignTeacherRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addEditGroupTeacher(AssignTeacherRequest $request)
    {
        try {
            $studentGroupTeacher = StudentGroupTeacher::where('discipline_id', $request->disciplineId)
                ->whereHas('studyGroups', function($query) use ($request){
                    $query->where('study_group_id', $request->groupId);
                })->first();

            if (empty($studentGroupTeacher)){
                $studentGroupTeacher = new StudentGroupTeacher;
                $studentGroupTeacher->discipline_id = $request->disciplineId;
                $studentGroupTeacher->save();

                $studentGroupTeacher->studyGroups()->sync([$request->groupId]);
            }
            $studentGroupTeacher->teacher_id = $request->teacherId;
            $studentGroupTeacher->save();

            return response()->json(['message' => 'Преподаватель был назначен!']);
        } catch (\Exception $e){
            return response()->json(['message' => 'Не удалось назначить преподавателя!'], 422);
        }
    }

    /**
     * @param GroupingStudyGroupsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function groupingStudyGroups(GroupingStudyGroupsRequest $request)
    {
        try {
            StudentGroupTeacher::where('discipline_id', $request->disciplineId)
                ->whereHas('studyGroups', function ($query) use ($request){
                    $query->whereIn('study_group_id', $request->groupsIds);
                })->delete();
            $studentGroupTeacher = new StudentGroupTeacher;
            $studentGroupTeacher->discipline_id = $request->disciplineId;
            $studentGroupTeacher->save();
            $studentGroupTeacher->studyGroups()->sync($request->groupsIds);

            return response()->json(['message' => 'Группы были сгруппированы!']);
        } catch (\Exception $e){
            return response()->json(['message' => 'Не удалось сгруппировать преподавателя!'], 422);
        }
    }
}
