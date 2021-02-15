<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-12-19
 * Time: 21:43
 */

namespace  App\Services;

use App\{
    SpecialityDiscipline,
    StudentDiscipline,
    SyllabusTaskCoursePay
};
use Illuminate\Support\Facades\{Log};

class SyllabusTaskService
{


    /**
     * получение информации об оплате курсовой, ее наличии у дисциплины и др
     * @param null $iDisciplineId
     * @return array
     */
    public static function getCoursePayData( $iDisciplineId=null )
    {

        $bIsPayedCourse = false;                                                  // оплачивалась ли курсовая
        $bIsPayedCourseOk = false;                                                // если оплачивалась курсовая и был обсчет
        $bIsPayedCoursePercent = false;                                           // если оплачивалась курсовая и есть %
        $bIsDisciplineHasCourse = false;                                          // наличие курсовой у дисциплины
        $bUserHasNotTaskPoints = false;                                           // наличие оценки у юзера по дисциплине

        if( empty($iDisciplineId) )
        {
            return [$bIsPayedCourse,$bIsPayedCourseOk,$bIsPayedCoursePercent,$bIsDisciplineHasCourse,$bUserHasNotTaskPoints];
        }

        $oSpecialityDiscipline = SpecialityDiscipline::
        where('speciality_id',Auth::user()->studentProfile->education_speciality_id)->
        where('discipline_id',$iDisciplineId)->
        first();

        if( !empty($oSpecialityDiscipline) )
        {

            // проверяем наличие курсовой у дисциплины
            $bIsDisciplineHasCourse = !empty($oSpecialityDiscipline->has_coursework) ? true : false;

            // проверяем оплачивалась ли курсовая
            $oSyllabusTaskCoursePay = SyllabusTaskCoursePay::
            where('discipline_id',$iDisciplineId)->
            where('user_id',Auth::user()->id)->
            first();
            if( !empty($oSyllabusTaskCoursePay) )
            {
                $bIsPayedCourse = true;
                if( $oSyllabusTaskCoursePay->status == SyllabusTaskCoursePay::STATUS_OK )
                {
                    $bIsPayedCourseOk = true;
                }
            }

            // проверяем наличие оценки у юзера по дисциплине
            $oStudentDiscipline = StudentDiscipline::
            where('discipline_id',$iDisciplineId)->
            where('student_id',Auth::user()->id)->
            first();
            $bUserHasNotTaskPoints = (!empty($oStudentDiscipline) && empty($oStudentDiscipline->task_result) && empty($oStudentDiscipline->task_result_points)) ? true : false;
            if( !empty($oStudentDiscipline) && !empty($oStudentDiscipline->task_result) )
            {
                $bIsPayedCoursePercent = $oStudentDiscipline->task_result;
            }

            unset($oSyllabusTaskCoursePay,$oStudentDiscipline);

        }
        unset($oSpecialityDiscipline);

        return [$bIsPayedCourse,$bIsPayedCourseOk,$bIsPayedCoursePercent,$bIsDisciplineHasCourse,$bUserHasNotTaskPoints];

    }


}