<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-10-31
 * Time: 16:22
 */

namespace App\Http\Controllers\Admin;

use Auth;
use App\{Syllabus,SyllabusTask,SyllabusTaskAnswer,SyllabusTaskQuestions};
use App\Http\Controllers\Controller;
use App\Services\{HtmlHelper, SyllabusService};
use App\Validators\{
    AdminSyllabusTaskGetListValidator,
    AdminSyllabusTaskEditAnswerValidator,
    AdminSyllabusTaskEditQuestionValidator,
    AdminSyllabusTaskEditTaskValidator,
    AdminSyllabusTaskDeleteAnswerValidator,
    AdminSyllabusTaskDeleteQuestionValidator,
    AdminSyllabusTaskDeleteTaskValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{App,DB,Log,Response,Session};

class SyllabusTaskController extends Controller
{
    /**
     * @param Request $request
     */
    public function getList( Request $request )
    {

        // validation data
        $obValidator = AdminSyllabusTaskGetListValidator::make($request->all());
        if ($obValidator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $obValidator->errors()
            ]);
        }

        $oSyllabusTask = SyllabusTask::
        with('questions')->
        with('questions.answer')->
        with('taskResultAll')->
        where('discipline_id',$request->input('discipline_id'))->
        where('language',$request->input('language'))->
        whereNull('deleted_at')->
        get();

        if( !empty($oSyllabusTask) )
        {
            foreach( $oSyllabusTask as &$itemT )
            {
                // проверяем доступ
                if( env('ADMIN_SYLLABUS_TASK_EDIT', false) || (Auth::user()->id == 96) || empty($itemT->taskResultAll)  )
                {
                    $itemT->isNotAccess = false;
                } elseif( !empty($itemT->taskResultAll) && (count($itemT->taskResultAll) > 0) )
                {
                    $itemT->isNotAccess = true;
                }
                if( !empty($itemT->questions) )
                {
                    foreach( $itemT->questions as &$itemQ )
                    {
                        $itemQ->answer_count = $itemQ->answerCount();
                        $itemQ->answer_correct = $itemQ->answerCorrect();
                        $itemQ->answer_uncorrect = $itemQ->answerUncorrect();
                    }
                }
            }
        }

        //

        return Response::json([
            'status'  => true,
            'models'  => $oSyllabusTask
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editTask(Request $request)
    {
        // validation data
        $obValidator = AdminSyllabusTaskEditTaskValidator::make($request->all());
        if ($obValidator->fails()) {
            return Response::json(
                [
                    'status' => false,
                    'message' => $obValidator->errors()
                ]
            );
        }

        if ($request->has('id') && ($request->input('id') != 0)) {
            $oSyllabusTask = SyllabusTask::where('id', $request->input('id'))->first();
        } else {
            $oSyllabusTask = new SyllabusTask();
        }

        // test syllabus task point
        $oSyllabusTasks = SyllabusTask::
        where('syllabus_id', $request->input('model.syllabus_id'))->
        whereNull('deleted_at')->
        get();

        $iCount = 0;
        if (!empty($oSyllabusTasks) && (count($oSyllabusTasks) > 0)) {
            foreach ($oSyllabusTasks as $itemST) {
                if (($oSyllabusTask->id) && ($itemST->id != $oSyllabusTask->id)) {
                    $iCount += $itemST->points;
                }
            }
            if ($iCount > 20) {
                return Response::json(
                    [
                        'status' => false,
                        'message' => 'Ошибка! В заданиях больше 20 баллов'
                    ]
                );
            }
        }

        $oSyllabusTask->fill($request->input('model'));

        $bLoadFiles = $oSyllabusTask->saveData(
            $request->input('img_source'),
            $request->input('audio_source')
        );

        if (empty($bLoadFiles)) {
            return Response::json(
                [
                    'status' => false,
                    'message' => 'Ошибка! Слишком большой размер файла'
                ]
            );
        }

        // check tasks points
        $oSyllabusTasks = SyllabusTask::
        where('syllabus_id', $request->input('model.syllabus_id'))->
        whereNotIn('id', [$oSyllabusTask->id])->
        whereNull('deleted_at')->
        get();
        if (!empty($oSyllabusTasks)) {
            $iSumPoints = 0;
            foreach ($oSyllabusTasks as $oItems) {
                $iSumPoints += $oItems->points;
            }
            if (($iSumPoints + $oSyllabusTask->points) > 20) {
                return Response::json(
                    [
                        'status' => false,
                        'message' => 'Ошибка! Сумма баллов заданий превышает 20'
                    ]
                );
            }
        }
        unset($oSyllabusTasks);

        // check questions points
        if (!empty($oSyllabusTask->questions)) {
            $iSum = 0;
            foreach ($oSyllabusTask->questions as $question) {
                $iSum += $question->points;
            }
            if ($iSum > $oSyllabusTask->points) {
                return Response::json(
                    [
                        'status' => false,
                        'message' => 'Ошибка! Баллы в вопросах больше баллов в задании'
                    ]
                );
            }
        }


        try {
            $oSyllabusTask->save();
        } catch (\Exception $e) {
            return Response::json(
                [
                    'status' => false,
                    'message' => __('Request error')
                ]
            );
        }


        return Response::json(
            [
                'status' => true,
                'message' => __('Success')
            ]
        );
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editQuestion(Request $request)
    {
        // validation data
        $obValidator = AdminSyllabusTaskEditQuestionValidator::make($request->all());
        if ($obValidator->fails()) {
            return Response::json(
                [
                    'status' => false,
                    'message' => $obValidator->errors()
                ]
            );
        }

        if ($request->has('id') && ($request->input('id') != 0)) {
            $oSyllabusTaskQuestion = SyllabusTaskQuestions::where('id', $request->input('id'))->first();
        } else {
            $oSyllabusTaskQuestion = new SyllabusTaskQuestions();
        }

        $oSyllabusTaskQuestion->fill($request->input('model'));

//        $oSyllabusTaskQuestion->question = $request->input('model')['question'];

        // check questions points
        $SyllabusTask = SyllabusTask::
        where('id', $request->input('model.task_id'))->
        with('questions')->
        whereNull('deleted_at')->
        first();
        if (!empty($SyllabusTask) && !empty($SyllabusTask->questions)) {
            $iSumPoints = 0;
            foreach ($SyllabusTask->questions as $oItems) {
                if ($oItems->id != $oSyllabusTaskQuestion->id) {
                    $iSumPoints += $oItems->points;
                }
            }
            if (($iSumPoints + $oSyllabusTaskQuestion->points) > $SyllabusTask->points) {
                return Response::json(
                    [
                        'status' => false,
                        'message' => 'Ошибка! Сумма баллов вопросов превышает баллы в задании'
                    ]
                );
            }
        }
        unset($SyllabusTask);


        try {
            $oSyllabusTaskQuestion->save();
        } catch (\Exception $e) {
            return Response::json(
                [
                    'status' => false,
                    'message' => __('Request error')
                ]
            );
        }

        return Response::json(
            [
                'status' => true,
                'message' => __('Success')
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editAnswer(Request $request)
    {
        // validation data
        $obValidator = AdminSyllabusTaskEditAnswerValidator::make($request->all());
        if ($obValidator->fails()) {
            return Response::json(
                [
                    'status' => false,
                    'message' => $obValidator->errors()
                ]
            );
        }

        if ($request->has('id') && ($request->input('id') != 0)) {
            $oSyllabusTaskAnswer = SyllabusTaskAnswer::where('id', $request->input('id'))->first();
        } else {
            $oSyllabusTaskAnswer = new SyllabusTaskAnswer();
        }

        $oSyllabusTaskAnswer->fill($request->input('model'));


        // check answer points
        $oSyllabusTaskQuestions = SyllabusTaskQuestions::
        with('answer')->
        where('id', $request->input('model.question_id'))->
        first();
        if (!empty($oSyllabusTaskQuestions) && !empty($oSyllabusTaskQuestions->answer)) {
            $iSumPoints = 0;
            foreach ($oSyllabusTaskQuestions->answer as $oItems) {
                if ($oItems->id != $oSyllabusTaskAnswer->id) {
                    $iSumPoints += $oItems->points;
                }
            }
            if (($iSumPoints + $oSyllabusTaskAnswer->points) > $oSyllabusTaskQuestions->points) {
                return Response::json(
                    [
                        'status' => false,
                        'message' => 'Ошибка! Сумма баллов ответов превышает баллы в вопросе'
                    ]
                );
            }
        }
        unset($SyllabusTask);


        try {
            $oSyllabusTaskAnswer->save();
        } catch (\Exception $e) {
            return Response::json(
                [
                    'status' => false,
                    'message' => __('Request error')
                ]
            );
        }

        return Response::json(
            [
                'status' => true,
                'message' => __('Success')
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTask( Request $request )
    {

        // validation data
        $obValidator = AdminSyllabusTaskDeleteTaskValidator::make($request->all());
        if ($obValidator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $obValidator->errors()
            ]);
        }

        $oSyllabusTask = SyllabusTask::
        where('id',$request->input('task_id'))->
        whereNull('deleted_at')->
        first();

        $oSyllabusTask->removeData();
        $oSyllabusTask->delete();

        return Response::json([
            'status' => true
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeQuestion( Request $request )
    {

        // validation data
        $obValidator = AdminSyllabusTaskDeleteQuestionValidator::make($request->all());
        if ($obValidator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $obValidator->errors()
            ]);
        }

        $oSyllabusTaskQuestions = SyllabusTaskQuestions::
        where('id',$request->input('question_id'))->
        whereNull('deleted_at')->
        first();

        $oSyllabusTaskQuestions->removeData();
        $oSyllabusTaskQuestions->delete();

        return Response::json([
            'status' => true
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAnswer( Request $request )
    {

        // validation data
        $obValidator = AdminSyllabusTaskDeleteAnswerValidator::make($request->all());
        if ($obValidator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $obValidator->errors()
            ]);
        }

        $oSyllabusTaskAnswer = SyllabusTaskAnswer::
        where('id',$request->input('answer_id'))->
        whereNull('deleted_at')->
        first();

        $oSyllabusTaskAnswer->delete();

        return Response::json([
            'status' => true
        ]);

    }


}