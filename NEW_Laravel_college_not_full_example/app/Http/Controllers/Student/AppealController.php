<?php

namespace App\Http\Controllers\Student;

use App\Appeal;
use App\AppealQuizAnswer;
use App\Discipline;
use App\QuizResult;
use App\StudentDiscipline;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppealController extends Controller
{
    public function test1Create(int $disciplineId)
    {
        $studentDiscipline = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($studentDiscipline)) {
            $this->flash_danger('Student-discipline link does not exist');
            return redirect()->route('study');
        }

        // Already exists - redirect to view
        $appealId = Appeal::getExistsId($studentDiscipline->id, StudentDiscipline::CONTROL_TYPE_TEST1);
        if (!empty($appealId)) {
            return redirect()->route('studentViewAppeal', ['appealId' => $appealId]);
        }

        if (!$studentDiscipline->test1_appeal_available) {
            $this->flash_danger('An appeal is available within 24 hours after testing');
            return redirect()->route('study');
        }

        $quizResult = QuizResult::getLastTest1($studentDiscipline->id);

        if (empty($quizResult)) {
            $this->flash_danger('Error. Result does not exist.');
            return redirect()->route('study');
        }

        $discipline = Discipline::getById($disciplineId);

        return view('student.appeals.create_test1', compact('studentDiscipline', 'discipline', 'quizResult'));
    }

    /**
     * Adding process
     * @param int $disciplineId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function test1CreatePost(int $disciplineId, Request $request)
    {
        $studentDiscipline = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($studentDiscipline)) {
            $this->flash_danger('Student-discipline link does not exist');
            return redirect()->route('study');
        }

        if (!$studentDiscipline->test1_appeal_available) {
            $this->flash_danger('An appeal is available within 24 hours after testing');
            return redirect()->route('study');
        }

        $validator = Validator::make($request->all(), [
            'quiz_result_id' => 'required',
            'reason' => 'required|max:1000',
            'file' => 'file|mimes:jpg,jpeg,gif,png'
        ]);

        if ($validator->fails()) {
            return redirect()->route('studentTest1Appeal', ['disciplineId' => $disciplineId])->withInput()->withErrors($validator);
        }

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('', ['disk' => 'appeals']);
        }

        $lastResult = QuizResult::getLastTest1($studentDiscipline->id);

        if ($lastResult->id != $request->input('quiz_result_id')) {
            $this->flash_danger('Error. This result is not the last.');
            return redirect()->route('study');
        }

        $appealId = Appeal::add(
            StudentDiscipline::CONTROL_TYPE_TEST1,
            $lastResult,
            $request->input('reason'),
            $filePath ?? null
        );

        if ($appealId) {
            // Save questions and answers
            $lastResult->addSnapshot($appealId);

            $this->flash_success('An appeal has been filed and will be reviewed by experts. You will be notified.');
            return redirect()->route('studentViewAppeal', ['appealId' => $appealId]);
        } else {
            $this->flash_danger('Error submitting appeal. Contact support.');
            return redirect()->route('studentTest1Appeal', ['disciplineId' => $disciplineId]);
        }
    }

    public function examCreate(int $disciplineId)
    {
        $studentDiscipline = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($studentDiscipline)) {
            $this->flash_danger('Student-discipline link does not exist');
            return redirect()->route('study');
        }

        // Already exists - redirect to view
        $appealId = Appeal::getExistsId($studentDiscipline->id, StudentDiscipline::CONTROL_TYPE_EXAM);
        if (!empty($appealId)) {
            return redirect()->route('studentViewAppeal', ['appealId' => $appealId]);
        }

        if (!$studentDiscipline->exam_appeal_available) {
            $this->flash_danger('An appeal is available within 24 hours after testing');
            return redirect()->route('study');
        }

        $quizResult = QuizResult::getLastExam($studentDiscipline->id);

        if (empty($quizResult)) {
            $this->flash_danger('Error. Result does not exist.');
            return redirect()->route('study');
        }

        $discipline = Discipline::getById($disciplineId);

        return view('student.appeals.create_exam', compact('studentDiscipline', 'discipline', 'quizResult'));
    }

    /**
     * Adding process
     * @param int $disciplineId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function examCreatePost(int $disciplineId, Request $request)
    {
        $studentDiscipline = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($studentDiscipline)) {
            $this->flash_danger('Student-discipline link does not exist');
            return redirect()->route('study');
        }

        if (!$studentDiscipline->exam_appeal_available) {
            $this->flash_danger('An appeal is available within 24 hours after testing');
            return redirect()->route('study');
        }

        $validator = Validator::make($request->all(), [
            'quiz_result_id' => 'required',
            'reason' => 'required|max:1000',
            'file' => 'file|mimes:jpg,jpeg,gif,png'
        ]);

        if ($validator->fails()) {
            return redirect()->route('studentExamAppeal', ['disciplineId' => $disciplineId])->withInput()->withErrors($validator);
        }

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('', ['disk' => 'appeals']);
        }

        $lastResult = QuizResult::getLastExam($studentDiscipline->id);

        if ($lastResult->id != $request->input('quiz_result_id')) {
            $this->flash_danger('Error. This result is not the last.');
            return redirect()->route('study');
        }

        $appealId = Appeal::add(
            StudentDiscipline::CONTROL_TYPE_EXAM,
            $lastResult,
            $request->input('reason'),
            $filePath ?? null
        );

        if ($appealId) {
            // Save questions and answers
            $lastResult->addSnapshot($appealId);

            $this->flash_success('An appeal has been filed and will be reviewed by experts. You will be notified by SMS.');
            return redirect()->route('studentViewAppeal', ['appealId' => $appealId]);
        } else {
            $this->flash_danger('Error submitting appeal. Contact support.');
            return redirect()->route('studentExamAppeal', ['disciplineId' => $disciplineId]);
        }
    }

    public function view(int $appealId)
    {
        $appeal = Appeal::where('id', $appealId)->first();

        if (empty($appeal)) {
            $this->flash_danger('Appeal does not exist');
            return redirect()->route('study');
        }

        if ($appeal->user_id != Auth::user()->id) {
            $this->flash_danger('Access denied');
            return redirect()->route('study');
        }

        $discipline = $appeal->studentDiscipline->discipline;
        $quizResult = $appeal->quizResult;

        return view('student.appeals.view', compact('appeal', 'discipline', 'quizResult'));
    }
}
