<?php
/**
 * User: dadicc
 * Date: 12/27/19
 * Time: 4:55 PM
 */

namespace  App\Services;

use App\{
    StudentDiscipline,
    SyllabusTask,
    SyllabusTaskResult,
    SyllabusTaskResultAnswer
};
use Illuminate\Support\Facades\Log;

class SRORecalculationService
{

    public static function SRORecalculation()
    {

        // перерасчет СРО
        // общая выборка
        $oStudentDiscipline = StudentDiscipline::
        where('task_result','!=',null)->
        where('plan_semester','2019-20.2')->
        get();


        if( !empty($oStudentDiscipline) && (count($oStudentDiscipline) > 0) )
        {
            foreach( $oStudentDiscipline as $iSDKey => $itemSD )
            {

                $iCurSDKey         = $iSDKey;
                $iCurUserId        = $itemSD->student_id;
                $iCurDisciplineId  = $itemSD->discipline_id;

                // вызываем перерасчет СРО и сохранение результатов
                StudentDiscipline::setSROResult($iCurUserId,$iCurDisciplineId);

            }
        }
        unset($oStudentDiscipline);

    }


}