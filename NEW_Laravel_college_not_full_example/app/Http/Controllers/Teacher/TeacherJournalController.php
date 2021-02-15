<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Student\StudentDisciplineSemesterRating;
use App\Models\Student\StudentGroupTeacher;
use App\Profiles;
use App\Semester;
use App\Services\Auth;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use App\StudentSchedule;
use App\StudyGroup;
use App\Http\Controllers\Controller;
use App\TimetableSchedule;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherJournalController extends Controller
{
    protected $teacherId;

    public function __construct(Request $request, $teacherId = null)
    {
        if (Auth::check()) {
            $this->teacherId = $request->get('teacher_id') ?? Auth::user()->id;
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $teacherId = $this->teacherId;
        $disciplines = Auth::user()->teacherDisciplines;

        return view('teacher.journal.index', compact('disciplines', 'teacherId'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxStudyGroups(Request $request)
    {
        $groups = User::find($this->teacherId)->teacherStudyGroups()
            ->where('discipline_id', $request->get('discipline'))
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return response()->json([
            'studyGroups' => $groups,
        ]);
    }

    public function ajaxSemesters(Request $request)
    {
        $groupId = $request->get('studyGroup');
        $disciplineId = $request->get('disciplineId');
      
	    $semesters = DB::select("select students_disciplines.plan_semester from students_disciplines
								left join profiles on profiles.user_id = students_disciplines.student_id
								where profiles.study_group_id = '".$groupId."'
									  and students_disciplines.discipline_id = '".$disciplineId."'
										  group by students_disciplines.plan_semester");

        return response()->json([
            'semesters' => $semesters
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxJournal(Request $request)
    {
        $disciplineId = $request->get('discipline');
        $studyGroupId = $request->get('studyGroup');
        $semester = $request->get('semester');

        $schedules = TimetableSchedule::where([
            'teacher_id' => $this->teacherId,
            'discipline_id' => $disciplineId,
            'study_group_id' => $studyGroupId,
            'semester' => $semester,
        ])->with('studentsLessons')->orderBy('date')->get();

        $students = Profiles::where('study_group_id', $studyGroupId)
            ->select(['user_id', 'study_group_id', 'fio', 'education_study_form', 'education_lang'])
            ->with(['studentsDisciplines' => function ($query) use ($disciplineId, $semester) {
                $query->where('discipline_id', $disciplineId)
                    ->where('plan_semester', $semester)
//                    ->where('plan_student_confirm', '>', 0)
//                    ->where('plan_admin_confirm', '>', 0)
                ;
            }])->get();

        foreach ($students as $key => $student) {
            if ($student->studentsDisciplines->isEmpty()) {
                $students->forget($key);
            }
        }

        return response()->json([
            'schedules' => $schedules,
            'students' => $students,
            'studentsIds' => $students->pluck('user_id')->toArray(),
            'lessons' => $schedules->pluck('studentsLessons'),
        ]);
    }

    public function ajaxAddToSchedule(Request $request)
    {
        $toSchedule = $request->get('schedule');
        $disciplineId = $request->get('disciplineId');
        $studyGroupId = $request->get('studyGroup');
        $teacherId = $request->get('teacher_id');
        $semester = $request->get('semester');
        $studentsIds = $request->get('studentsIds');

        $schedule = TimetableSchedule::where([
            'discipline_id' => $disciplineId,
            'teacher_id' => $teacherId,
            'study_group_id' => $studyGroupId,
            'semester' => $semester,
            'date' => Carbon::createFromFormat('d.m.Y', $toSchedule['date'])->toDateString()
        ])->with('studentsLessons')->first();

        if ($schedule) {
            $schedule->lesson_type = $toSchedule['lesson_type'];
            $schedule->topic = $toSchedule['topic'];
            $schedule->save();
        } else {
            $schedule = TimetableSchedule::create([
                'discipline_id' => $disciplineId,
                'teacher_id' => $teacherId,
                'study_group_id' => $studyGroupId,
                'semester' => $semester,
                'date' => Carbon::createFromFormat('d.m.Y', $toSchedule['date'])->toDateString(),
                'lesson_type' => $toSchedule['lesson_type'],
                'topic' => $toSchedule['topic'],
            ]);

            $lessons = [];
            if (!isset($toSchedule['id'])) {
                foreach ($studentsIds as $id) {
                    $studentLesson = new StudentSchedule;
                    $studentLesson->user_id = $id;
                    $studentLesson->timetable_schedules_id = $schedule->id;
                    $studentLesson->save();

                    $lessons[] = $studentLesson;
                }
            }

            $schedule->studentsLessons = $lessons;
        }

        return response()->json([
            'schedule' => $schedule,
            'lessons' => $schedule->studentsLessons
        ]);
    }

    public function ajaxSetRating(Request $request)
    {
        if (false && !$request->get('fromAdmin')) {
            return \Response::json([
                'status' => false,
                'message' => 'В данный момент редактирование журнала запрещено.'
            ]);
        }

        $studentLesson = StudentSchedule::find($request->get('lesson_id'));
        $studentLesson->rating = ($request->get('rating') > 100) ? 100 : $request->get('rating');
        $studentLesson->save();

        return response()->json([
            'lesson' => $studentLesson,
        ]);
    }

    public function ajaxSetFinalResult(Request $request)
    {
        if (false && !$request->get('fromAdmin')) {
            return \Response::json([
                'status' => false,
                'message' => 'В данный момент редактирование журнала запрещено.'
            ]);
        }

        $studentDiscipline = StudentDiscipline::find($request->get('sd_id'));
        $studentDiscipline->final_result_points = ($request->get('rating') > 100) ? 100 : $request->get('rating');
        $studentDiscipline->save();

        return response()->json([
            'studentDiscipline' => $studentDiscipline,
        ]);
    }

}
