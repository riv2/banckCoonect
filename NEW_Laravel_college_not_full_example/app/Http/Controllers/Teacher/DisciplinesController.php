<?php

namespace App\Http\Controllers\Teacher;

use App\Discipline;
use App\ManualResult;
use App\Models\StudentDisciplineDay;
use App\Models\StudentDisciplineFile;
use App\Profiles;
use App\Semester;
use App\StudentDiscipline;
use App\StudentPracticeFiles;
use App\StudyGroup;
use App\StudyGroupTeacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class DisciplinesController extends Controller
{
    public function list()
    {
        $disciplines = Auth::user()->teacherDisciplines;

        return view('teacher.disciplines.list', compact(['disciplines']));
    }

    public function groups(int $disciplineId)
    {
        $discipline = Discipline
            ::with('studentDisciplineDayLimits')
            ->where('id', $disciplineId)
            ->first();

        $groups = Auth::user()->getTeacherDisciplineGroups($disciplineId);

        return view('teacher.disciplines.groups', compact(['groups', 'discipline']));
    }

    public function group(int $disciplineId, int $groupId)
    {
        $discipline = Discipline
            ::with(['studentDisciplineDayLimits' => function($query) use ($groupId){
                $query->where('study_group_id', $groupId);
                $query->orderBy('day_num');
            }])
            ->where('id', $disciplineId)
            ->first();

        if (empty($discipline)) {
            $this->flash_warning('Дисциплина не существует');
            return redirect()->route('teacherDisciplines');
        }

        $group = StudyGroup::getById($groupId);

        if (empty($group)) {
            $this->flash_warning('Группа не привязана');
            return redirect()->route('teacherDisciplines');
        }

        $teacherGroup = StudyGroupTeacher::getOne(Auth::user()->id, $groupId, $disciplineId);

        if (empty($teacherGroup)) {
            $this->flash_warning('Группа-дисциплина не привязана');
            return redirect()->route('teacherDisciplines');
        }

        $isManualExamTime = $teacherGroup->isManualExamTime();

        $studentsProfiles = $group->studentsProfiles;

        StudentDiscipline::setDisciplineToStudents($studentsProfiles, $disciplineId);
        StudentDiscipline::setManualResultAccess($studentsProfiles, Auth::user()->id);
        StudentDiscipline::setRatingByDays($studentsProfiles, $disciplineId, $groupId);

        $currentSemester = Semester::current('fulltime');

        return view('teacher.disciplines.group', compact(['studentsProfiles', 'group', 'discipline', 'teacherGroup', 'isManualExamTime', 'currentSemester']));
    }

    public function groupSave(int $disciplineId, int $groupId, Request $request)
    {
        if (empty($request->input('data'))) {
            return redirect()->route('teacherGroup', ['group_id' => $groupId, 'discipline_id' => $disciplineId]);
        }

        foreach ($request->input('data') as $studentId => $results) {

            $SD = StudentDiscipline::getOne($studentId, $disciplineId);

            $SD->week1_result = $results['week1_result'];
            $SD->week2_result = $results['week2_result'];
            $SD->week3_result = $results['week3_result'];
            $SD->week4_result = $results['week4_result'];
            $SD->week5_result = $results['week5_result'];
            $SD->week6_result = $results['week6_result'];

            $SD->week7_result = $results['week8_result'];
            $SD->week9_result = $results['week9_result'];
            $SD->week10_result = $results['week10_result'];
            $SD->week11_result = $results['week11_result'];
            $SD->week12_result = $results['week12_result'];
            $SD->week13_result = $results['week13_result'];
            $SD->week14_result = $results['week14_result'];
            $SD-> week15_result= $results['week15_result'];

            $SD->week16_result = $results['week16_result'];
            $SD->week17_result = $results['week17_result'];
            $SD->week18_result = $results['week18_result'];
            $SD->week19_result = $results['week19_result'];
            $SD->week20_result = $results['week20_result'];

            $dayRatings = $results['day_rating'] ?? [];

            foreach ($dayRatings as $day => $rating)
            {
                $studentDisciplineDay = StudentDisciplineDay
                    ::where('user_id', $studentId)
                    ->where('discipline_id', $disciplineId)
                    ->where('day_num', $day)
                    ->first();

                if(isset($rating['rating']))
                {
                    if(empty($studentDisciplineDay))
                    {
                        $profile = Profiles::where('user_id', $studentId)->first();

                        $studentDisciplineDay = new StudentDisciplineDay();
                        $studentDisciplineDay->user_id = $studentId;
                        $studentDisciplineDay->discipline_id = $disciplineId;
                        $studentDisciplineDay->semester = Semester::current($profile->education_study_form);
                        $studentDisciplineDay->day_num = $day;
                    }

                        $studentDisciplineDay->teacher_id = \App\Services\Auth::user()->id;
                        $studentDisciplineDay->rating = $rating['rating'] > 100 ? 100 : $rating['rating'];
                        $studentDisciplineDay->date = $rating['date'];
                        $studentDisciplineDay->save();
                }
                else
                {
                    if($studentDisciplineDay)
                    {
                        $studentDisciplineDay->delete();
                    }
                }

            }

            $SD->save();

            // Test1
//            if ($results['test1_result'] !== null) {
//                $results['test1_result'] = StudentDiscipline::resultClean($results['test1_result']);
//
//                // New val
//                if ($SD->test1_result != $results['test1_result']) {
//                    $SD->setTest1ResultManual($results['test1_result']);
//
//                    if ($SD->task_result !== null && $SD->test_result !== null) {
//                        $SD->calculateFinalResult();
//                    }
//                }
//            }

            // SRO
            if (!empty($results['sro'])) {
                $results['sro'] = StudentDiscipline::resultClean($results['sro']);

                // New val
                if ($SD->task_result != $results['sro']) {
                    $oldValue = $SD->task_result;
                    $SD->setSROResultManual($results['sro']);

                    if ($SD->test1_result !== null && $SD->test_result !== null) {
                        $SD->calculateFinalResult();
                    }

                    // Save log
                    $log = new ManualResult();
                    $log->teacher_id = Auth::user()->id;
                    $log->discipline_id = $disciplineId;
                    $log->study_group_id = $groupId;
                    $log->student_id = $studentId;
                    $log->student_discipline_id = $SD->id;
                    $log->sro_old = $oldValue;
                    $log->sro_new = $results['sro'];
                    $log->save();
                }
            }

            // Exam
            if (!empty($results['exam'])) {
                $results['exam'] = StudentDiscipline::resultClean($results['exam']);

                // New val
                if ($SD->test_result != $results['exam']) {
                    $oldValue = $SD->test_result;

                    $SD->setExamResultManual($results['exam']);

                    // Practise
                    if ($SD->discipline->is_practice) {
                        $SD->setFinalResultManual($results['exam']);
                    } elseif ($SD->test1_result !== null && $SD->task_result !== null) {
                        $SD->calculateFinalResult();
                    }

                    // Save log
                    $log = new ManualResult();
                    $log->teacher_id = Auth::user()->id;
                    $log->discipline_id = $disciplineId;
                    $log->study_group_id = $groupId;
                    $log->student_id = $studentId;
                    $log->student_discipline_id = $SD->id;
                    $log->exam_old = $oldValue;
                    $log->exam_new = $results['exam'];
                    if (!$log->save()) {
                        throw new \Exception('Cannot save manual log. SD ID '. $SD->id);
                    }
                }
            }
        }

        $this->flash_success('Saved');

        return redirect()->route('teacherGroup', ['group_id' => $groupId, 'discipline_id' => $disciplineId]);
    }

    public function journalUploadStudentFile(Request $request, $discipline_id)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'file|mimes:doc,docx,pdf,xls,xlsx,ppt,pptx',
            'student_id' => 'required'
        ]);

        if($request->hasFile('document'))
        {
            $fileExt =  $request->file('document')->getClientOriginalExtension();

            if(!$fileExt)
            {
                return Response::json([
                    'status' => 'error',
                    'message' => __('Invalid file name. No extension found')
                ]);
            }
        }

        if ($validator->fails()){
            return Response::json([
                'status' => 'error',
                'mwssage' => __('The document must be a file of type: doc, docx, pdf, xls, xlsx, ppt, pptx')]
            );
        }

        $discipline = Discipline::where('id', $discipline_id)->first();

        if(!$discipline)
        {
            abort(404);
        }

        if($discipline->is_practice)
        {
            $file = new StudentPracticeFiles();
            $file->created_at = DB::raw('NOW()');
        }
        else
        {
            $file = new StudentDisciplineFile();
            $file->teacher_id = \App\Services\Auth::user()->id;
            $file->new_file = false;
        }

        if($request->input('link', null))
        {
            $file->type = 'link';
            $file->link = trim($request->input('link'));
        }
        else
        {
            $file->type = 'file';
            $file->saveFile($request->all());
        }

        $file->user_id = $request->input('student_id');
        $file->discipline_id = $discipline_id;
        $file->save();

        return Response::json([
            'status' => 'success',
            'file_name' => $file->file_name,
            'original_name' => $file->original_name,
            'id' => $file->id
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function journalDeleteStudentFile(Request $request)
    {
        $fileId = $request->input('file_id');

        $file = StudentDisciplineFile::where('id', $fileId)->where('teacher_id', \App\Services\Auth::user()->id)->first();

        if($file)
        {
            $file->delete();

            return Response::json([
                'status' => 'success'
            ]);
        }

        return Response::json([
            'status' => 'fail',
            'message' => __('File not found')
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function journalStudentFileSetRead(Request $request)
    {
        $fileId = $request->input('file_id');
        $file = StudentDisciplineFile::where('id', $fileId)->first();

        if($file)
        {
            $file->new_file = false;
            $file->save();

            return Response::json([
                'status' => 'success'
            ]);
        }

        return Response::json([
            'status' => 'fail',
            'message' => __('File not found')
        ]);
    }


}