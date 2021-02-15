<?php

namespace App\Http\Controllers\Student;

use App\Discipline;
use App\FinanceNomenclature;
use App\Language;
use App\Profiles;
use App\QrCode;
use App\QuizResult;
use App\QuizeResultKge;
use App\Semester;
use App\Services\LanguageService;
use App\SpecialityDiscipline;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\User;
use App\QuizQuestion;
use App\QuizAnswer;
use App\StudentDiscipline;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class QuizController extends Controller
{
    /**
     * Test 1. Not Exam
     * @param $disciplineId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function test1(int $disciplineId)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($SD)) {
            $this->flash_danger('There is not such discipline');
            return redirect()->route('study');
        }

        $SD->setTest1ButtonShow(Auth::user());
        if (!$SD->test1ButtonShow) {
            abort(404);
        }

        // Access
        if (!$SD->test1_available) {
            $this->flash_danger('In order to start the test you need to pay at least 1 credit');
            return redirect()->route('study');
        }

        // QR or remote required
        if (!$SD->test1_qr_checked && !$SD->remote_access && !Auth::user()->free_remote_access) {
            $this->flash_danger('To access the test, you must scan the QR or buy remote access');
            return redirect()->route('study');
        }

        // Test1 time
        if (Auth::user()->isTest1Time($SD->plan_semester)) {
            // Has not free attempts
            // FIXME на время карантина
            if (!$SD->hasTest1FreeAttemptCorona()) {
                // Not Test1 Retake time
                if (!Auth::user()->isTest1RetakeTime($SD->plan_semester)) {
                    return redirect()->route('study');
                }

                // Not trial - Need to buy trial
                if (!$SD->test1_result_trial) {
                    return redirect()->route('studentTest1Trial', ['id' => $disciplineId]);
                }
            }
        }
        // Not Test1 time && Test1 Retake time
        elseif (Auth::user()->isTest1RetakeTime($SD->plan_semester)) {
            // Not trial - Need to buy trial
            if (!$SD->test1_result_trial) {
                return redirect()->route('studentTest1Trial', ['id' => $disciplineId]);
            }
        }

        $language = $SD->getTestLanguage(Auth::user()->studentProfile);

        $questions = $SD->discipline->getQuizQuestions(
            $language,
            $SD->discipline->ects,
            true
        );

        $hasAudio = QuizQuestion::hasAudio($questions);

        $SD->test1_max_points = QuizQuestion::getMaxPointsFromArray($questions);
        $SD->save();

        shuffle($questions);

        // 20 min
        $timeLimit = 20*60;

        $exam = false;

        $hash = QuizResult::generateHash();

//        $webCamAccess = 0;
        $webCamAccess = $SD->remote_access || Auth::user()->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_ONLINE;
        $testType = 't1';
        return view('student.quiz', compact('questions', 'disciplineId', 'timeLimit', 'hasAudio', 'SD', 'exam', 'hash', 'webCamAccess', 'testType'));
    }

    /**
     * @param $disciplineId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function quizKge()
    {
        if (!\App\Services\Auth::user()->studentAllQuizSuccess() && !\App\Services\Auth::user()->keycloak) {
            abort(404);
        }

        $quizeResultCount = QuizeResultKge
            ::where('user_id', Auth::user()->id)
            ->where('payed', false)
            ->count();

        if (\App\Services\Auth::user()->keycloak && $quizeResultCount > 0) {
            abort(404);
        }

        if ($quizeResultCount > 1) {
            return redirect()->route('retakeKgePay');
        }

        $totalCorrectPoints = 0;

        $allQuestionsModels = \App\Services\Auth::user()->studentProfile->speciality->getKgeQuestionList(\App\Services\Auth::user()->studentProfile->education_lang);
        $allQuestionsModels = $allQuestionsModels ? $allQuestionsModels : [];
        $allQuestions = [];
        $hasAudio = false;

        foreach ($allQuestionsModels as $question) {
            $totalCorrectPoints = $totalCorrectPoints + $question->getMaxPoints();
            $allQuestions[] = $question;

            if (count($question->audiofiles) > 0) {
                $hasAudio = true;
            }
        }

        session()->put('kgeTotalCorrectPoints', $totalCorrectPoints);

        $timeLimit = 120 * 60;
        return view('student.quize', compact('allQuestions', 'timeLimit', 'hasAudio'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function quizKgeCheck(Request $request)
    {
        if (!\App\Services\Auth::user()->studentAllQuizSuccess() && !\App\Services\Auth::user()->keycloak) {
            abort(404);
        }

        $pointsTotal = 0;

        $validQuestionList = QuizQuestion
            ::select([
                'quize_questions.id as id'
            ])
            ->leftJoin('syllabus_quize_questions', 'syllabus_quize_questions.quize_question_id', '=', 'quize_questions.id')
            ->leftJoin('syllabus', 'syllabus_quize_questions.syllabus_id', '=', 'syllabus.id')
            ->leftJoin('students_disciplines', 'syllabus.discipline_id', '=', 'students_disciplines.discipline_id')
            ->leftJoin('speciality_discipline', 'speciality_discipline.discipline_id', '=', 'syllabus.discipline_id')
            ->where('students_disciplines.student_id', Auth::user()->id)
            ->where('speciality_discipline.exam', true)
            ->get();

        $validQuestionIdList = [];

        foreach ($validQuestionList as $item) {
            $validQuestionIdList[] = $item->id;
        }

        $questionList = $request->input('questionList');
        $answersForSave = [];

        foreach ($questionList as $item) {
            $question = QuizQuestion
                ::select('id', 'total_points')
                ->with(['answers' => function ($query) {
                    $query->select([
                        'id',
                        'question_id',
                        'points',
                        'correct'
                    ]);
                }])
                ->where('id', $item['id'])
                ->first();

            if (!is_array($item['answer'])) {
                $item['answer'] = [$item['answer']];
            }

            if (!count($item['answer'])) {
                $answersForSave[] = [
                    'question_id' => $question->id,
                    'answer_id' => null
                ];
            }

            $questionPoints = 0;

            if ($question->getCorrectAnswersCount() >= count($item['answer'])) {
                foreach ($question->answers as $answer) {
                    if (in_array($answer->id, $item['answer'])) {
                        $answersForSave[] = [
                            'question_id' => $question->id,
                            'answer_id' => $answer->id
                        ];
                        if ($answer->correct) {
                            $questionPoints += $answer->points;
                        } elseif (!$answer->correct && !$question->has_multi_answer) {
                            $questionPoints = 0;
                            break;
                        }
                    }
                }
            }

            $pointsTotal = $pointsTotal + $questionPoints;
            $allQuestions[] = $question;
        }

        $totalCorrectPoints = session()->get('kgeTotalCorrectPoints', null);
        $percentVal = ($pointsTotal * 100) / $totalCorrectPoints;

        $quizeResultCount = QuizeResultKge
            ::where('user_id', Auth::user()->id)
            ->where('payed', false)
            ->count();

        if ($quizeResultCount >= 1) {
            $percentVal = $percentVal + 10;

            if ($percentVal + 10 > 100) {
                $percentVal = 100;
            }
        }

        $percentVal = round($percentVal);

        $quizeResultKge = new QuizeResultKge();
        $quizeResultKge->user_id = \App\Services\Auth::user()->id;
        $quizeResultKge->value = $percentVal;
        $quizeResultKge->payed = false;

        $quizeResultKge->save();
        $quizeResultKge->setValue($percentVal);
        $quizeResultKge->setResultAnswers($answersForSave);

        return Response::json();
    }

    /**
     * Checking users's answers for Test 1
     * @param $disciplineId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function test1Check(int $disciplineId, Request $request)
    {
        $blur = $request->input('blur') ?? false;
        $hash = $request->input('hash') ?? null;

        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($SD)) {
            return Response::json(['success' => false, 'error' => 'There is not such discipline']);
        }

        $SD->setTest1ButtonShow(Auth::user());
        if (!$SD->test1ButtonShow) {
            return Response::json(['success' => false, 'error' => 'Not allowed']);
        }

        // Access
        if (!$SD->test1_available || (!$SD->test1_qr_checked && !$SD->remote_access && !Auth::user()->free_remote_access)) {
            return Response::json(['success' => false, 'error' => 'access']);
        }

        // Check hash
        if (empty($hash)) {
            return Response::json(['success' => false, 'error' => 'Hash is empty']);
        } elseif (QuizResult::existsByHash($SD->id, QuizResult::TYPE_TEST1, $hash)) {
            return Response::json(['success' => true, 'already_done' => true]);
        }

        // Test1 time
        if (Auth::user()->isTest1Time($SD->plan_semester)) {
            // Has not free attempts
            // FIXME на время карантина
            if (!$SD->hasTest1FreeAttemptCorona()) {
                // Not Test1 Retake time
                if (!Auth::user()->isTest1RetakeTime($SD->plan_semester)) {
                    return Response::json(['success' => false, 'error' => 'Not allowed']);
                }

                // Not trial - Need to buy trial
                if (!$SD->test1_result_trial) {
                    return Response::json(['success' => false, 'error' => 'Need to buy trial']);
                }
            }
        }
        // Not Test1 time && Test1 Retake time
        elseif (Auth::user()->isTest1RetakeTime($SD->plan_semester)) {
            // Not trial - Need to buy trial
            if (!$SD->test1_result_trial) {
                return Response::json(['success' => false, 'error' => 'Need to buy trial']);
            }
        }

        [$pointsTotal, $answersForSave, $percentsOfSelectedAnswers] = QuizQuestion::getPointTotalAndAnswers($request->input('questionList'));

        $testResult = round(($pointsTotal * 100) / $SD->test1_max_points);

        // Not Blur
        if (!$blur && $percentsOfSelectedAnswers >= 80 && $testResult < 70) {
            // FIXME на время эпидемии
            if ($SD->isTest1PaidAttemptCorona()) {
                $testResult = rand(70, 79);
            }

            // Remote access FIXME отключено на время эпидемии
//        if ((!empty($SD->remote_access) || Auth::user()->distance_learning) {
//            $testResult = rand(70, 79);
//        }
        }

        $language = $SD->getTestLanguage(Auth::user()->studentProfile);

        $quizResult = QuizResult::addTest1(
            Auth::user()->id,
            $language,
            $SD->discipline_id,
            $SD->id,
            $hash,
            $testResult,
            $blur
        );

        if (!empty($quizResult)) {
            $quizResult->setResultAnswers($answersForSave);
            Auth::user()->updateGpa();
        }

        // Set Best Result
        if (!$SD->setTest1Result()) {
            return Response::json(['success' => false, 'error' => 'Cannot save result to SD']);
        }

        if ($SD->test_result !== null && $SD->task_result !== null) {
            // Calc final result
            $SD->calculateFinalResult();
        }

        return Response::json(['success' => true]);
    }

    /**
     * @param $disciplineId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
//    public function quizResult($disciplineId)
//    {
//        $studentsDiscipline = StudentDiscipline
//            ::where('discipline_id', $disciplineId)
//            ->where('student_id', Auth::user()->id)
//            //->whereNull('test_result')
//            ->first();
//
//        if (!$studentsDiscipline) {
//            abort(404);
//        }
//
//        $percentVal = $studentsDiscipline->test_result;
//        $letter = $studentsDiscipline->test_result_letter;
//
//        return view('student.quizeResult', compact('percentVal', 'studentsDiscipline', 'letter'));
//    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function quizKgeResult()
    {
        $quizResultKge = QuizeResultKge
            ::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$quizResultKge) {
            abort(404);
        }

        $percentVal = $quizResultKge->value;
        $letter = $quizResultKge->letter;

        return view('student.quizeResult', compact('percentVal', 'letter'));
    }

    public function test1method(int $disciplineId)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($SD)) {
            $this->flash_danger('There is not such discipline');
            return redirect()->route('study');
        }

        $SD->setTest1ButtonShow(Auth::user());
        if (!$SD->test1ButtonShow) {
            return redirect()->route('study');
        }

        if (!$SD->test1_available) {
            $this->flash_danger('In order to start the test you need to pay at least 1 credits');
            return redirect()->route('study');
        }

        // Test1 time
        if (Auth::user()->isTest1Time($SD->plan_semester)) {
            // Has not free attempts
            // FIXME на время эпидемии
            if (!$SD->hasTest1FreeAttemptCorona()) {
                // Not Test1 Retake time
                if (!Auth::user()->isTest1RetakeTime($SD->plan_semester)) {
                    return redirect()->route('study');
                }

                // Not trial - Need to buy trial
                if (!$SD->test1_result_trial) {
                    return redirect()->route('studentTest1Trial', ['id' => $disciplineId]);
                }
            }
        }
        // Not Test1 time && Test1 Retake time
        elseif (Auth::user()->isTest1RetakeTime($SD->plan_semester)) {
            // Not trial - Need to buy trial
            if (!$SD->test1_result_trial) {
                return redirect()->route('studentTest1Trial', ['id' => $disciplineId]);
            }
        }

        // Has remote access - to test
        if ($SD->remote_access || Auth::user()->free_remote_access) {
            return redirect()->route('studentQuiz', ['id' => $disciplineId]);
        }

        return view('student.test1.method', ['studentDiscipline' => $SD]);
    }

//    public function test1Remote(int $disciplineId)
//    {
//        // TODO проверку для Теста2
//        if (!Profiles::isTest1Time(Auth::user()->studentProfile->education_study_form)) {
//            abort(404);
//        }
//
//        $studentDiscipline = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);
//        $studentDiscipline->setTest1Available();
//
//        if (!$studentDiscipline->test1Available) {
//            abort(404);
//        }
//
//        $discipline = Discipline::getById($disciplineId);
//
//        if (empty($studentDiscipline) || empty($discipline)) {
//            abort(404);
//        }
//
//        // Already remote
//        if ($studentDiscipline->remote_access || Auth::user()->distance_learning) {
//            return redirect()->route('studentQuiz', ['id' => $disciplineId]);
//        }
//
//        // Already passed and not trial
//        if (!empty($studentDiscipline->test1_result) && !$studentDiscipline->test1_result_trial) {
//            return redirect()->route('studentTest1Result', ['id' => $disciplineId]);
//        }
//
//        $service = FinanceNomenclature::getTest1Remote($discipline->ects);
//        $lowBalance = Auth::user()->balance < $service->cost;
//
//        $localeNameField = Language::getFieldName('name', app()->getLocale());
//
//        return view('student.test1remote', compact('studentDiscipline', 'discipline', 'localeNameField', 'lowBalance'));
//    }

    public function test1QRCheck(Request $request)
    {
        $code = $request->input('code');
        $disciplineId = $request->input('discipline_id');

        if (empty($code) || empty($disciplineId)) {
            return Response::json([
                'status' => false,
                'error' => __('Code or discipline ID is empty')
            ]);
        }

        // FIXME Temporary
//        if (QrCode::isValid($disciplineId, $code)) {
        if (QrCode::isValid(1, $code)) {
            StudentDiscipline::allowTest1InClassroom(Auth::user()->id, $disciplineId);

            return Response::json(['status' => true]);
        } else {
            return Response::json([
                'status' => false,
                'error' => __('QR code not found or invalid')
            ]);
        }
    }

    public function test1NumericCodeCheck(Request $request)
    {
        $code = $request->input('code');
        $disciplineId = $request->input('discipline_id');

        if (empty($code) || empty($disciplineId)) {
            return Response::json([
                'status' => false,
                'error' => 'Code or discipline ID is empty'
            ]);
        } elseif (!is_numeric($code)) {
            return Response::json([
                'success' => false,
                'error' => __('Code has to be numeric')
            ]);
        }

        // FIXME Temporary
//        if (QrCode::isNumericCodeValid($disciplineId, $code)) {
        if (QrCode::isNumericCodeValid(1, $code)) {
            StudentDiscipline::allowTest1InClassroom(Auth::user()->id, $disciplineId);

            return Response::json(['status' => true]);
        } else {
            return Response::json([
                'status' => false,
                'error' => __('Numeric code not found or invalid'),
                'code' => $code,
                'discipline_id' => $disciplineId
            ]);
        }
    }

    public function test1Result(int $disciplineId)
    {
        $studentsDiscipline = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($studentsDiscipline) || $studentsDiscipline->test1_result === null) {
            abort(404);
        }

        $maxTest1Points = StudentDiscipline::TEST1_MAX_POINTS;

        return view('student.test1.result', compact('studentsDiscipline', 'maxTest1Points'));
    }

    public function test1LastResult(int $disciplineId)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($SD) || $SD->test1_result === null) {
            abort(404);
        }

        $result = QuizResult::getLastTest1($SD->id);

        $maxTest1Points = StudentDiscipline::TEST1_MAX_POINTS;

        $showTrialButton = Auth::user()->isTest1Time($SD->plan_semester) || Auth::user()->isTest1RetakeTime($SD->plan_semester);

        return view('student.test1.last_result', compact('result', 'maxTest1Points', 'SD', 'showTrialButton'));
    }

    public function test1Trial(int $disciplineId)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($SD)) {
            abort(404);
        }

        // Not Test1 Retake time
        if (!Auth::user()->isTest1RetakeTime($SD->plan_semester)) {
            $this->flash_danger('Error. It is not Test1 retake time now.');
            return redirect()->route('study');
        }

        // Already trial
        if ($SD->test1_result_trial) {
            return redirect()->route('studentSelectTest1Method', ['id' => $disciplineId]);
        }

        // FIXME на время эпидемии
        if ($SD->corona_distant || $SD->remote_access) {
            // Test1 time && Has free attempts - to SelectMethod
            if (Auth::user()->isTest1Time($SD->plan_semester) && $SD->hasTest1FreeAttemptCorona()) {
                return redirect()->route('studentSelectTest1Method', ['id' => $disciplineId]);
            }
        } else {
            // Test1 time && Has free attempts - to SelectMethod
            if (Auth::user()->isTest1Time($SD->plan_semester) && $SD->hasTest1FreeAttempt()) {
                return redirect()->route('studentSelectTest1Method', ['id' => $disciplineId]);
            }
        }

        $service = FinanceNomenclature::getTest1Trial();
        $lowBalance = Auth::user()->balance < $service->cost;

        return view('student.test1.trial', compact('SD', 'service', 'lowBalance'));
    }

    public function examMethod(int $disciplineId)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);
        
        if (empty($SD)) {
            $this->flash_danger('There is not such discipline');
            return redirect()->route('study');
        }

        // Унаследованная
        if ($SD->is_inherited) {
            $this->flash_danger('It is forbidden to retake inherited disciplines');
            return redirect()->route('study');
        }

        // Traditional form
        // FIXME на время карантина проверка отключена. Потом вернуть
//        if (
//            $SD->discipline->control_form == Discipline::CONTROL_FORM_TRADITIONAL &&
//            !$SD->remote_access &&
//            Auth::user()->studentProfile->education_study_form != Profiles::EDUCATION_STUDY_FORM_ONLINE
//        ) {
//            return view('student.exam.traditional');
//        }

        $SD->setExamButtonShow(Auth::user());
        if (!$SD->examButtonShow) {
           // return redirect()->route('study');
        }

        // Access
        if (!$SD->exam_available) {
            if (Auth::user()->distance_learning) {
             //   $this->flash_danger('To access the exam, you must completely buy the discipline');
            } else {
             //   $this->flash_danger('To access the exam you must completely buy the discipline and pass the Test 1');
            }

         //   return redirect()->route('study');
        }

        // Exam time
        if (Auth::user()->isExamTime($SD->plan_semester)) {
            // Has not free attempts
            // FIXME на время эпидемии
            if (!$SD->hasExamFreeAttemptCorona()) {
                // Not Exam Retake time
                if (!Auth::user()->isExamRetakeTime($SD->plan_semester)) {
                    return redirect()->route('study');
                }

                // Not trial - Need to buy trial
                if (!$SD->test_result_trial) {
                    return redirect()->route('studentExamTrial', ['id' => $disciplineId]);
                }
            }
        }
        // Not Exam time && Exam Retake time
        elseif (Auth::user()->isExamRetakeTime($SD->plan_semester)) {
            // Not trial - Need to buy trial
            if (!$SD->test_result_trial) {
                return redirect()->route('studentExamTrial', ['id' => $disciplineId]);
            }
        }

        //todo disable qr
        /*if (
            $studentDiscipline->remote_access &&
            Auth::user()->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_FULLTIME &&
            !Auth::user()->studentProfile->remote_exam_qr
        ) {
            return redirect()->route('studentRemoteExamQR');
        }*/

        // Has remote access - to exam
        if ($SD->remote_access || Auth::user()->free_remote_access) {
            return redirect()->route('studentExam', ['id' => $disciplineId]);
        }

        return view('student.exam.method', compact('SD'));
    }

    /**
     * Exam
     * @param $disciplineId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function exam(int $disciplineId)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($SD)) {
            $this->flash_danger('There is not such discipline');
            return redirect()->route('study');
        }

        // Унаследованная
        if ($SD->is_inherited) {
            $this->flash_danger('It is forbidden to retake inherited disciplines');
            return redirect()->route('study');
        }

        $SD->setExamButtonShow(Auth::user());
        if (!$SD->examButtonShow) {
         //   return redirect()->route('study');
        }

        // Access
        if (!$SD->exam_available) {
            if (Auth::user()->distance_learning) {
              //  $this->flash_danger('To access the exam, you must completely buy the discipline');
            } else {
              //  $this->flash_danger('To access the exam you must completely buy the discipline and pass the Test 1');
            }

          //  return redirect()->route('study');
        }

        // Exam time
        if (Auth::user()->isExamTime($SD->plan_semester)) {
            // Has not free attempts
            // FIXME на время эпидемии
            if (!$SD->hasExamFreeAttemptCorona()) {
                // Not Exam Retake time
                if (!Auth::user()->isExamRetakeTime($SD->plan_semester)) {
                    return redirect()->route('study');
                }

                // Not trial - Need to buy trial
                if (!$SD->test_result_trial) {
                    return redirect()->route('studentExamTrial', ['id' => $disciplineId]);
                }
            }
        }
        // Not Exam time && Exam Retake time
        elseif (Auth::user()->isExamRetakeTime($SD->plan_semester)) {
            // Not trial - Need to buy trial
            if (!$SD->test_result_trial) {
                return redirect()->route('studentExamTrial', ['id' => $disciplineId]);
            }
        }

        // QR or remote required
        if (
            !$SD->test_qr_checked &&
            !$SD->remote_access &&
            !Auth::user()->free_remote_access &&
            Auth::user()->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_FULLTIME
        ) {
            $this->flash_danger('To access the exam, you must scan the QR or buy remote access');
            return redirect()->route('study');
        }

        $language = $SD->getTestLanguage(Auth::user()->studentProfile);

        $questions = $SD->discipline->getQuizQuestions($language, $SD->discipline->ects);

        $hasAudio = QuizQuestion::hasAudio($questions);

        $SD->test_max_points = QuizQuestion::getMaxPointsFromArray($questions);
        $SD->save();

        shuffle($questions);

        $timeLimit = 60 * 2 * count($questions);

        $exam = true;
        $hash = QuizResult::generateHash();

        $webCamAccess = $SD->remote_access || Auth::user()->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_ONLINE;
        $testType = 'exam';
        $studentDiscipline = $disciplineId;
        return view('student.quiz', compact('questions', 'disciplineId', 'timeLimit', 'hasAudio', 'SD', 'exam', 'hash', 'webCamAccess', 'studentDiscipline', 'testType'));
    }

    /**
     * Checking users's answers for Exam
     * @param $disciplineId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function examCheck(int $disciplineId, Request $request)
    {
        $blur = $request->input('blur') ?? false;
        $hash = $request->input('hash') ?? null;

        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);
        if (empty($SD)) {
            return Response::json(['success' => false, 'error' => 'There is not such discipline']);
        }

        // Унаследованная
        if ($SD->is_inherited) {
            return Response::json(['success' => false, 'error' => 'It is forbidden to retake inherited disciplines']);
        }

        // Check hash
        if (empty($hash)) {
            return Response::json(['success' => false, 'error' => 'Hash is empty']);
        } elseif (QuizResult::existsByHash($SD->id, QuizResult::TYPE_EXAM, $hash)) {
            return Response::json(['success' => true, 'already_done' => true]);
        }

        $SD->setExamButtonShow(Auth::user());
        if (!$SD->examButtonShow) {
            return Response::json(['success' => false, 'error' => 'Button does not show']);
        }

        // Access
        if (
            !$SD->exam_available ||
            (
                !$SD->test_qr_checked &&
                !$SD->remote_access &&
                !Auth::user()->free_remote_access &&
                Auth::user()->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_FULLTIME
            )
        ) {
            return Response::json(['success' => false, 'error' => 'access']);
        }

        // Exam time
        if (Auth::user()->isExamTime($SD->plan_semester)) {
            // Has not free attempts
            // FIXME на время эпидемии
            if (!$SD->hasExamFreeAttemptCorona()) {
                // Not Exam Retake time
                if (!Auth::user()->isExamRetakeTime($SD->plan_semester)) {
                    return Response::json(['success' => false, 'error' => 'Not allowed']);
                }

                // Not trial - Need to buy trial
                if (!$SD->test_result_trial) {
                    return Response::json(['success' => false, 'error' => 'Has not attempts']);
                }
            }
        }
        // Not Exam time && Exam Retake time
        elseif (Auth::user()->isExamRetakeTime($SD->plan_semester)) {
            // Not trial - Need to buy trial
            if (!$SD->test_result_trial) {
                return Response::json(['success' => false, 'error' => 'Has not attempts']);
            }
        }

        [$pointsTotal, $answersForSave, $percentsOfSelectedAnswers] = QuizQuestion::getPointTotalAndAnswers($request->input('questionList'));

        $testResult = round(($pointsTotal * 100) / $SD->test_max_points);

        // Not Blur
        if (!$blur && $percentsOfSelectedAnswers >= 80 && $testResult < 70) {
            // FIXME на время эпидемии
            if ($SD->isExamPaidAttemptCorona()) {
                $testResult = rand(70, 79);
            }

            // FIXME отключено на время эпидемии
            // Distance Learning
//            if (Auth::user()->distance_learning) {
//                if ($SD->exam_attempts_count > 0) {
//                    $testResult = rand(70, 79);
//                }
//            }
//            // Remote access
//            elseif ($SD->remote_access) {
//                if ($SD->exam_attempts_count > 0) {
//                    $testResult = rand(70, 79);
//                }
//            }
//            // Paid attempt
//            elseif ($SD->isExamPaidAttempt()) {
//                $testResult = rand(70, 79);
//            }
        }

        $language = $SD->getTestLanguage(Auth::user()->studentProfile);

        $quizResult = QuizResult::addExam(
            Auth::user()->id,
            $language,
            $SD->discipline_id,
            $SD->id,
            $hash,
            $testResult,
            $blur
        );

        if (!empty($quizResult)) {
            Auth::user()->updateGpa();
            $quizResult->setResultAnswers($answersForSave);
        }

        // Set Best Result
        $SD->setExamResult();

        // Calc final result
        $SD->calculateFinalResult();

        return Response::json(['success' => true]);
    }

    public function examLastResult(int $disciplineId)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($SD) || $SD->test_result === null) {
            abort(404);
        }

        $result = QuizResult::getLastExam($SD->id);

        if (empty($result)) {
            abort(404);
        }

        $maxExamPoints = StudentDiscipline::EXAM_MAX_POINTS;

        return view('student.exam.last_result', compact('result', 'maxExamPoints', 'SD'));
    }

    public function examTrial(int $disciplineId)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($SD)) {
            abort(404);
        }

        // Already trial
        if ($SD->test_result_trial) {
            return redirect()->route('studentSelectExamMethod', ['id' => $disciplineId]);
        }

        // FIXME на время эпидемии
        if ($SD->corona_distant || $SD->remote_access) {
            // Exam time && Has free attempts - to SelectMethod
            if (Auth::user()->isExamTime($SD->plan_semester) && $SD->hasExamFreeAttemptCorona()) {
                return redirect()->route('studentSelectExamMethod', ['id' => $disciplineId]);
            }
        } else {
            // Exam time && Has free attempts - to SelectMethod
            if (Auth::user()->isExamTime($SD->plan_semester) && $SD->hasExamFreeAttempt()) {
                return redirect()->route('studentSelectExamMethod', ['id' => $disciplineId]);
            }
        }

        // Not Exam Retake time
        if (!Auth::user()->isExamRetakeTime($SD->plan_semester)) {
            $this->flash_danger('Error. It is not Exam retake time now.');
            return redirect()->route('study');
        }

        $service = FinanceNomenclature::getExamTrial();
        $lowBalance = Auth::user()->balance < $service->cost;

        return view('student.exam.trial', compact('SD', 'service', 'lowBalance'));
    }

    public function examQRCheck(Request $request)
    {
        $code = $request->input('code');
        $disciplineId = $request->input('discipline_id');

        if (empty($code) || empty($disciplineId)) {
            return Response::json(
                [
                    'status' => false,
                    'error' => __('Code or discipline ID is empty')
                ]
            );
        }

        // FIXME Temporary
//        if (QrCode::isValid($disciplineId, $code)) {
        if (QrCode::isValid(1, $code)) {
            StudentDiscipline::allowExamInClassroom(Auth::user()->id, $disciplineId);

            return Response::json(['status' => true]);
        } else {
            return Response::json(
                [
                    'status' => false,
                    'error' => __('QR code not found or invalid')
                ]
            );
        }
    }

    public function examNumericCodeCheck(Request $request)
    {
        $code = $request->input('code');
        $disciplineId = $request->input('discipline_id');

        if (empty($code) || empty($disciplineId)) {
            return Response::json(
                [
                    'status' => false,
                    'error' => 'Code or discipline ID is empty'
                ]
            );
        }

        // FIXME Temporary
//        if (QrCode::isNumericCodeValid($disciplineId, $code)) {
        if (QrCode::isNumericCodeValid(1, $code)) {
            StudentDiscipline::allowExamInClassroom(Auth::user()->id, $disciplineId);

            return Response::json(['status' => true]);
        } else {
            return Response::json(
                [
                    'status' => false,
                    'error' => __('Numeric code not found or invalid'),
                    'code' => $code,
                    'discipline_id' => $disciplineId
                ]
            );
        }
    }

    public function remoteQR()
    {
        return view('student.exam.remote_qr');
    }

    public function remoteExamQRCheck(Request $request)
    {
        $code = $request->input('code');

        if (empty($code)) {
            return Response::json(
                [
                    'status' => false,
                    'error' => __('Code is empty')
                ]
            );
        }

        if (QrCode::isValid(1, $code)) {
            Auth::user()->studentProfile->allowRemoteExam();
            return Response::json(['status' => true]);
        } else {
            return Response::json(
                [
                    'status' => false,
                    'error' => __('QR code not found or invalid')
                ]
            );
        }
    }

    public function remoteExamNumericCodeCheck(Request $request)
    {
        $code = $request->input('code');

        if (empty($code)) {
            return Response::json(
                [
                    'status' => false,
                    'error' => 'Code is empty'
                ]
            );
        }

        if (QrCode::isNumericCodeValid(1, $code)) {
            Auth::user()->studentProfile->allowRemoteExam();
            return Response::json(['status' => true]);
        } else {
            return Response::json(
                [
                    'status' => false,
                    'error' => __('Numeric code not found or invalid'),
                    'code' => $code
                ]
            );
        }
    }
}