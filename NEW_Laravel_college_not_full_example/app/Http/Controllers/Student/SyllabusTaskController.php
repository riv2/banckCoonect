<?php
/**
 * User: dadicc
 * Date: 11/14/19
 * Time: 10:51 PM
 */

namespace App\Http\Controllers\Student;

use App\SyllabusTaskAnswer;
use Auth;
use App\{
    Discipline,
    Profiles,
    SpecialityDiscipline,
    StudentDiscipline,
    Syllabus,
    SyllabusTask,
    SyllabusTaskCoursePay,
    SyllabusTaskResult,
    SyllabusTaskResultAnswer,
    SyllabusTaskResultFiles,
    SyllabusTaskUserPay
};
use App\Http\Controllers\Controller;
use App\Services\{Service1C, SyllabusTaskService};
use App\Validators\{
    SyllabusTaskGetListValidator,
    SyllabusTaskPayValidator,
    SyllabusTaskPayPostValidator,
    SyllabusTaskProceedValidator,
    SyllabusTaskRenderListValidator,
    SyllabusTaskSaveResultValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{File, Image, Log, Response, Session, View};

class SyllabusTaskController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getList(Request $request)
    {
        // validation data
        $obValidator = SyllabusTaskGetListValidator::make($request->all());
        if ($obValidator->fails() || empty(Auth::user()->studentProfile)) {
            return redirect()->route('study')->withErrors([__('Discipline not found')]);
        }

        $SD = StudentDiscipline::getOne(Auth::user()->id, $request->input('discipline_id'));

        if (empty($SD)) {
            $this->flash_danger('There is not such discipline');
            return redirect()->route('study');
        }

        $SD->setSROButtonShow(Auth::user());
        if (!$SD->SROButtonShow) {
            return redirect()->route('study');
        }

        // Access
        if (!$SD->sro_available) {
            return redirect()->route('study');
        }

        $aLangList = [
            Profiles::EDUCATION_LANG_KZ,
            Profiles::EDUCATION_LANG_RU,
            Profiles::EDUCATION_LANG_EN
        ];

        return view(
            'student.syllabustask.list',
            [
                'discipline_id' => $request->input('discipline_id'),
                'lang' => $aLangList
            ]
        );
    }

    /**
     * render list
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function renderList(Request $request)
    {
        // validation data
        $obValidator = SyllabusTaskRenderListValidator::make($request->all());
        if ($obValidator->fails() || empty(Auth::user()->studentProfile)) {
            return \Response::json([
                'status' => false,
                'message' => __('Discipline not found')
            ]);
        }

        $studentDiscipline = StudentDiscipline::getOne(Auth::user()->id, $request->input('discipline_id'));

        // init
        $oSyllabusTasks = null;
        $aRes = [];
        $aUniqIds = [];
        $iPoints = 0;

        // выбор СРО с результатами /////////////////////////////////
        $oSyllabusTasks = SyllabusTask
            ::with('pay')
            ->with('payCount')
            ->where('discipline_id', $request->input('discipline_id'))
            ->whereHas('taskResult')
            ->whereNull('deleted_at')
            ->get();

        if ($oSyllabusTasks->isNotEmpty()) {
            foreach ($oSyllabusTasks as $itemST) {
                $aUniqIds[] = $itemST->id;
                $aRes[] = $itemST;
                $iPoints += $itemST->points;
            }
        }

        /////////////////////////////////////////////////////////////

        $oSyllabusTasksElective = [];
        if (!empty(Auth::user()->studentProfile->elective_speciality_id)) {
            // выбор СРО elective
            // get language
            $oSpecialityDiscipline = SpecialityDiscipline
                ::where('speciality_id', Auth::user()->studentProfile->elective_speciality_id)
                ->where('discipline_id', $request->input('discipline_id'))
                ->first();

            if (!empty($oSpecialityDiscipline)) {
                // lang - $oSpecialityDiscipline->getLangForSRO()
                $oSyllabusTasksElective = SyllabusTask::getSyllabusTaskData($request->input('discipline_id'), $oSpecialityDiscipline->getLangForSRO());
            } else {
                $oSyllabusTasksElective = SyllabusTask::getSyllabusTaskData($request->input('discipline_id'), Auth::user()->studentProfile->education_lang);
            }

            // test points
            if ($oSyllabusTasksElective->isNotEmpty()) {
                // предварительный анализ
                $iPointsSTE = 0;
                foreach ($oSyllabusTasksElective as $itemSTE) {
                    $iPointsSTE += $itemSTE->points;
                }
                // если есть результаты то сбрасываем текущие
                if ($iPointsSTE > 5) {
                    $aRes = [];
                    $iPoints = 0;
                }

                foreach ($oSyllabusTasksElective as $itemSTE) {
                    /** @var SyllabusTask $itemSTE */

                    // FIXME CORONA
                    $itemSTE->setProceedButtonShowCorona(Auth::user());
                    $itemSTE->setRetakeButtonShowCorona(Auth::user());
                    $aRes[] = $itemSTE;
                    $iPoints += $itemSTE->points;
                }

            }
        }

        if (count($oSyllabusTasksElective) < 1) {
            // выбор СРО по языку ///////////////////////////////////////
            // get language
            $oSpecialityDiscipline = SpecialityDiscipline::getOne(
                Auth::user()->studentProfile->education_speciality_id,
                $request->input('discipline_id')
            );

            $oSyllabusTasksOther = SyllabusTask::getSyllabusTaskData($request->input('discipline_id'), $request->input('lang'));

            // test points
            if ($oSyllabusTasksOther->isNotEmpty()) {
                // предварительно проверяем кол-во баллов
                $iSTOPoints = 0;
                foreach ($oSyllabusTasksOther as $itemSTOL) {
                    $iSTOPoints += $itemSTOL->points;
                }

                // если получили больше 5 баллов сбрасываем
                if ($iSTOPoints > 5) {
                    $aRes = [];
                    $iPoints = 0;
                }

                foreach ($oSyllabusTasksOther as $itemSTO) {
                    /** @var SyllabusTask $itemSTO */

                    // FIXME CORONA
                    $itemSTO->setProceedButtonShowCorona(Auth::user());
                    $itemSTO->setRetakeButtonShowCorona(Auth::user());
                    $aRes[] = $itemSTO;
                    $iPoints += $itemSTO->points;
                }
            }
        }

        // проверка на 40 баллов
        if ($iPoints != 20) {
            $aRes = null;
        }

        // SRO COURSE
        $bIsPayedCourse = false;                                                  // оплачивалась ли курсовая
        $bIsPayedCourseOk = false;                                                // если оплачивалась курсовая и был обсчет
        $bIsPayedCoursePercent = false;                                           // если оплачивалась курсовая и есть %
        $bIsDisciplineHasCourse = false;                                          // наличие курсовой у дисциплины
        $bUserHasNotTaskPoints = false;                                           // наличие оценки у юзера по дисциплине
        if (!empty($oSpecialityDiscipline) && !empty($oSpecialityDiscipline->has_coursework)) {
            [$bIsPayedCourse, $bIsPayedCourseOk, $bIsPayedCoursePercent, $bIsDisciplineHasCourse, $bUserHasNotTaskPoints] = SyllabusTaskService::getCoursePayData($request->input('discipline_id'));
        }

        // Verbal SRO
        if (
            $studentDiscipline->discipline->verbal_sro &&
            Auth::user()->studentProfile->education_study_form != Profiles::EDUCATION_STUDY_FORM_ONLINE &&
            !$studentDiscipline->remote_access
        ) {
            return View::make(
                'student.syllabustask.verbal_sro',
                [
                    'discipline_id' => $request->input('discipline_id'),
                    'bIsPayedCourse' => $bIsPayedCourse,
                    'bIsPayedCourseOk' => $bIsPayedCourseOk,
                    'bIsPayedCoursePercent' => $bIsPayedCoursePercent,
                    'bIsDisciplineHasCourse' => $bIsDisciplineHasCourse,
                    'bUserHasNotTaskPoints' => $bUserHasNotTaskPoints
                ]
            );
        }

        return View::make(
            'student.syllabustask.renderPartialList',
            [
                'syllabusTask' => $aRes,
                'discipline_id' => $request->input('discipline_id'),
                'bIsPayedCourse' => $bIsPayedCourse,
                'bIsPayedCourseOk' => $bIsPayedCourseOk,
                'bIsPayedCoursePercent' => $bIsPayedCoursePercent,
                'bIsDisciplineHasCourse' => $bIsDisciplineHasCourse,
                'bUserHasNotTaskPoints' => $bUserHasNotTaskPoints,
                'remote_access' => $studentDiscipline->remote_access ? true : false,
                'fulltime' => Auth::user()->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_FULLTIME
            ]
        );
    }

    /**
     * Passing quiz
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function proceed(Request $request)
    {
        // validation data
        $obValidator = SyllabusTaskProceedValidator::make($request->all());
        if ($obValidator->fails()) {
            return redirect()->back()->withErrors([__('SRO not found')]);
        }

        $task = SyllabusTask
            ::with('questions')
            ->with('pay')
            ->where('id', $request->input('task_id'))
            ->first();

        if (empty($task)) {
            $this->flash_danger('Task does not exists');
            return redirect()->route('study');
        }

        $SD = StudentDiscipline::getOne(Auth::user()->id, $task->discipline_id);

        if (empty($SD)) {
            $this->flash_danger('There is not such discipline');
            return redirect()->route('study');
        }

        $SD->setSROButtonShow(Auth::user());
        if (!$SD->SROButtonShow) {
            $this->flash_danger('!SROButtonShow');
            return redirect()->route('study');
        }

        // Access
        if (!$SD->sro_available) {
            $this->flash_danger('!sro_available');
            return redirect()->route('study');
        }

        // SRO time
        if (Auth::user()->isSROTime($SD->plan_semester)) {
            // Has not free attempts
            // FIXME на время карантина
            if (!$task->hasFreeAttemptCorona(Auth::user()->id)) {
                // Not SRO Retake time
                if (!Auth::user()->isSRORetakeTime($SD->plan_semester)) {
                    $this->flash_danger('!isSRORetakeTime');
                    return redirect()->route('study');
                }

                // Not trial - Need to buy trial
                if (!$task->isTrial(Auth::user()->id)) {
                    return redirect()->route('sroTaskPay', ['task_id' => $task->id]);
                }
            }
        }
        // Not SRO time && SRO Retake time
        elseif (Auth::user()->isSRORetakeTime($SD->plan_semester)) {
            // Not trial - Need to buy trial
            if (!$task->isTrial(Auth::user()->id)) {
                return redirect()->route('sroTaskPay', ['task_id' => $task->id]);
            }
        }

        if (!empty($task->questions) && (count($task->questions) > 0)) {
            $aQuestions = [];
            $aNewQuestions = [];

            foreach ($task->questions as $iKey => &$itemQ) {
                $aQuestions[] = $iKey;

                if (!empty($itemQ->answer) && (count($itemQ->answer) > 0)) {
                    $count = 0;
                    $aAnswer = [];
                    $aNewAnswer = [];

                    foreach ($itemQ->answer as $key => $itemA) {
                        $aAnswer[] = $key;
                        if (!empty($itemA->correct)) {
                            $count += 1;
                        }
                    }

                    shuffle($aAnswer);

                    foreach ($aAnswer as $itemAnswer) {
                        $oItemAnswer = $itemQ->answer[$itemAnswer];
                        $oItemAnswer->answer = str_replace(['\'', '"', '`'], '', $oItemAnswer->answer);
                        $aNewAnswer[] = $oItemAnswer;
                    }

                    $itemQ->answer = $aNewAnswer;

                    if ($count > 1) {
                        $itemQ->multi = $count;
                    }
                }

                $itemQ->question = str_replace(['\'', '"', '`'], '', $itemQ->question);
            }

            shuffle($aQuestions);

            foreach ($aQuestions as $itemKey) {
                $aNewQuestions[] = $task->questions[$itemKey];
            }

            $task->questions = $aNewQuestions;
        }

        // time limit 20 min
        $iTimeLimit = 60 * 20;

        return view(
            'student.syllabustask.process',
            [
                'task' => $task,
                'timeLimit' => $iTimeLimit,
                'discipline_id' => $request->input('discipline_id')
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveResult(Request $request)
    {
        // validation data
        $obValidator = SyllabusTaskSaveResultValidator::make($request->all());

        if ($obValidator->fails()) {
            return \Response::json(
                [
                    'status' => false,
                    'message' => __('Error input data')
                ]
            );
        }

        $task = SyllabusTask
            ::with('payCount')
            ->where('id', $request->input('questionList.task_id'))
            ->first();

        if (empty($task)) {
            return \Response::json([
                'status' => false,
                'message' => 'Task does not exist'
            ]);
        }

        // init
        $iInputAnswersCount = 0;                                                                           // кол-во проставленных ответов
        $iTotalCorrectAnswersCount = SyllabusTask::getTotalCorrectAnswersByTaskId($task->id);   // кол-во правильных ответов
        $iInputCorrectPoints = 0;                                                                          // правильные отмеченных баллы
        $iTotalCorrectPoints = $task->points;                                                     // правильные баллы задания
        $aAnswersData = [];

        $SD = StudentDiscipline::getOne(Auth::user()->id, $task->discipline_id);

        if (empty($SD)) {
            return \Response::json([
                'status' => false,
                'message' => 'Error. There is not such discipline'
            ]);
        }

        $SD->setSROButtonShow(Auth::user());
        if (!$SD->SROButtonShow) {
            return \Response::json([
                'status' => false,
                'message' => 'Error. Button is not visible'
            ]);
        }

        // Access
        if (!$SD->sro_available) {
            return \Response::json([
                'status' => false,
                'message' => 'Error. Button is not available'
            ]);
        }

        // SRO time
        if (Auth::user()->isSROTime($SD->plan_semester)) {
            // Has not free attempts
            // FIXME на время карантина
            if (!$task->hasFreeAttemptCorona(Auth::user()->id)) {
                // Not SRO Retake time
                if (!Auth::user()->isSRORetakeTime($SD->plan_semester)) {
                    return \Response::json(['status' => false, 'message' => 'Not allowed']);
                }

                // Not trial - Need to buy trial
                if (!$task->isTrial(Auth::user()->id)) {
                    return \Response::json(['status' => false, 'message' => 'Has not attempts']);
                }
            }
        }
        // Not SRO time && SRO Retake time
        elseif (Auth::user()->isSRORetakeTime($SD->plan_semester)) {
            // Not trial - Need to buy trial
            if (!$task->isTrial(Auth::user()->id)) {
                return \Response::json(['status' => false, 'message' => 'Has not attempts']);
            }
        }

        // если тип СРО не эссе
        if ($task->type != SyllabusTask::TYPE_ESSAY) {
            // get points
            $aAnswers = $request->input('questionList.answers');
            [$aAnswersData, $iInputCorrectPoints, $iInputAnswersCount] = SyllabusTask::testProcessSaveResultGetData($aAnswers);

            // фиксируем промежуточные данные
            $taskResult = new SyllabusTaskResult();
            $taskResult->fill($request->input('questionList'));
            $taskResult->user_id = Auth::user()->id;

            // получаем % ответов
            $iInputPercent = $task->getPercent($iInputCorrectPoints);

            // анализ ответов
            if ($iInputPercent >= 70) {
                // сам сдал, фиксируем результат
                $taskResult->points = $iInputCorrectPoints;
                $taskResult->value = $iInputPercent;
            } else {
                // набрал мало баллов
                // смотрим сколько проставлено ответов
                // находим % проставленных ответов
                $iInputAnswersPercent = intval(($iInputAnswersCount * 100) / $iTotalCorrectAnswersCount);

                // FIXME на время эпидемии
                if ($iInputAnswersPercent >= 80 && $task->isTrial(Auth::user()->id)) {
                    $taskResult->value = rand(70, 79);
                    $taskResult->points = intval(($taskResult->value * $iTotalCorrectPoints) / 100);
                }

                // если удаленный доступ и прокликал больше 79%
                // и вторая попытка то
//                if (!empty($SD->remote_access) && $iInputAnswersPercent >= 80 && count($task->payCount) >= 1) {
//                    // выставляем студику рандомные баллы
//                    $taskResult->value = rand(70, 79);
//                    $taskResult->points = intval(($taskResult->value * $iTotalCorrectPoints) / 100);
//                } elseif ((count($task->payCount) >= 2) && ($iInputAnswersPercent >= 80)) {
//                    // выставляем студику рандомные баллы
//                    $taskResult->value = rand(70, 79);
//                    $taskResult->points = intval(($taskResult->value * $iTotalCorrectPoints) / 100);
//                }

                else {
                    // мало отметил, ставим что есть
                    $taskResult->points = $iInputCorrectPoints;
                    $taskResult->value = $iInputPercent;
                }
            }

            if ($taskResult->save()) {
                // save result answers
                foreach ($aAnswersData as $answer) {
                    $oSyllabusTaskResultAnswer = new SyllabusTaskResultAnswer();
                    $oSyllabusTaskResultAnswer->fill(
                        [
                            'question_id' => $answer->question_id,
                            'answer_id' => $answer->id,
                            'result_id' => $taskResult->id
                        ]
                    );
                    $oSyllabusTaskResultAnswer->save();
                    unset($oSyllabusTaskResultAnswer);
                }
            }

            // processing points and percent
            StudentDiscipline::setSROResult(Auth::user()->id, $request->input('discipline_id'));
        } else {
            // если тип СРО эссе

            //Log::info('data: ' . var_export($request->all(),true));

            if (!empty($request->input('image_essay')) && !empty($request->input('source_essay'))) {
                $oSyllabusTaskResultFiles = new SyllabusTaskResultFiles();
                $oSyllabusTaskResultFiles->fill(
                    [
                        'user_id' => Auth::user()->id,
                        'discipline_id' => $request->input('discipline_id'),
                        'syllabus_id' => $request->input('questionList.syllabus_id'),
                        'task_id' => $request->input('questionList.task_id'),
                    ]
                );

                // загрузка файла результата
                $oSyllabusTaskResultFiles->name = $request->input('image_essay');
                $oSyllabusTaskResultFiles->saveResultFile($request->input('source_essay'));

                $oSyllabusTaskResultFiles->save();
            }
        }

        // disabled can task exam
        $oSyllabusTaskUserPay = new SyllabusTaskUserPay();
        $oSyllabusTaskUserPay->fill(
            [
                'task_id' => $request->input('questionList.task_id'),
                'user_id' => Auth::user()->id,
                'active' => SyllabusTaskUserPay::STATUS_INACTIVE,
                'payed' => SyllabusTaskUserPay::STATUS_PAYED_INACTIVE
            ]
        );
        $oSyllabusTaskUserPay->save();

        return \Response::json(['status' => true]);
    }


    /**
     * @param Request $request
     * @return SyllabusTaskController|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function pay(Request $request)
    {
        // validation data
        $obValidator = SyllabusTaskPayValidator::make($request->all());
        if ($obValidator->fails() || empty(Auth::user()->id)) {
            return redirect()->
            route('sroTaskPay')->
            withErrors([__('Error input data')]);
        }


        $oSyllabusTask = SyllabusTask::
        where('id', $request->input('task_id'))->
        first();

        $oSyllabusTaskUserPay = SyllabusTaskUserPay::
        where('task_id', $request->input('task_id'))->
        where('user_id', Auth::user()->id)->
        first();

        if (empty($oSyllabusTask) || empty(Auth::user())) {
            return redirect()->
            route('sroTaskPay')->
            withErrors([__('Error input data')]);
        }

        // cost
        $iCost = intval(100 * $oSyllabusTask->points);

        return view(
            'student.syllabustask.pay',
            [
                'task' => $oSyllabusTask,
                'cost' => $iCost,
                'taskPay' => $oSyllabusTaskUserPay
            ]
        );
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payPost(Request $request)
    {
        $oSyllabusTask = SyllabusTask::
        where('id', $request->input('task_id'))->
        first();

        if (empty($oSyllabusTask) || empty(Auth::user()->id) || empty(Auth::user()->studentProfile)) {
            return redirect()->
            route('sroTaskPay', ['task_id' => $request->input('task_id')])->
            withErrors([__('Error input data')]);
        }

        // cost
        $iCost = intval(100 * $oSyllabusTask->points);

        // test user balance
        if ($iCost > Auth::user()->balanceByDebt()) {
            return redirect()->
            route('sroTaskPay', ['task_id' => $request->input('task_id')])->
            withErrors([__('Not enough funds on balance')]);
        }

        $bResponse = Service1C::pay(
            Auth::user()->studentProfile->iin,
            '00000003515',
            $iCost
        );

        if (!empty($bResponse)) {
            $oSyllabusTaskUserPay = new SyllabusTaskUserPay();
            $oSyllabusTaskUserPay->fill(
                [
                    'task_id' => $request->input('task_id'),
                    'user_id' => Auth::user()->id,
                    'active' => SyllabusTaskUserPay::STATUS_ACTIVE,
                    'payed' => SyllabusTaskUserPay::STATUS_PAYED_ACTIVE
                ]
            );
            $oSyllabusTaskUserPay->save();

            \Session::put('withoutBack', true);
            return redirect()->route('sroGetList', ['discipline_id' => $oSyllabusTask->discipline_id])->with('flash_message', __('You have successfully paid SRO'));
        }

        return redirect()->
        route('sroTaskPay', ['task_id' => $request->input('task_id')])->
        withErrors([__('Error input data')]);
    }


}