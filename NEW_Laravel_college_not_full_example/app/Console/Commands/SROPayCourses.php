<?php

namespace App\Console\Commands;

use App\{
    StudentDiscipline,
    SyllabusTaskCoursePay
};
use App\Services\{StudentRating};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Log};

class SROPayCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sro:pay:courses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        Log::info('--RUN SRO Pay Courses-- START ' . date('Y-m-d H:i:s'));


        // достаем записи
        // по порядку
        // по статусу "в обработке"
        // оценка выставляется через 10ч после покупки
        // лимит 500
        $oSyllabusTaskCoursePay = SyllabusTaskCoursePay::
        where('status',SyllabusTaskCoursePay::STATUS_PROCESS)->
        whereRaw('ABS(TIMESTAMPDIFF(HOUR, created_at, ?)) >= 10', [ date('Y-m-d H:i:s', time()) ])->  // часы
        //whereRaw('ABS(TIMESTAMPDIFF(MINUTE, created_at, ?)) >= 10', [ date('Y-m-d H:i:s', time()) ])->     //  минуты
        orderBy('id','ASC')->
        limit(500)->
        get();

        if( !empty($oSyllabusTaskCoursePay) && (count($oSyllabusTaskCoursePay) > 0) )
        {

            Log::info('FIND: ' . count($oSyllabusTaskCoursePay));

            //Log::info('data: ' . var_export($oSyllabusTaskCoursePay,true));

            foreach( $oSyllabusTaskCoursePay as $itemSTCP )
            {

                $oStudentDiscipline = StudentDiscipline::
                where('discipline_id',$itemSTCP->discipline_id)->
                where('student_id',$itemSTCP->user_id)->
                first();

                // получаем %
                $iPersent = rand(70,74);

                // получаем баллы
                $iPoints = intval( ($iPersent * 40) / 100 );

                $oStudentDiscipline->task_result = $iPersent;
                $oStudentDiscipline->task_result_points = $iPoints;
                $oStudentDiscipline->task_result_letter = StudentRating::getLetter($iPersent);
                $oStudentDiscipline->task_date = date('Y-m-d H:i:s');
                $oStudentDiscipline->task_manual = false;
                $oStudentDiscipline->task_blur = 0;

                // фиксируем результат
                $oStudentDiscipline->save();

                // вызываем общий пересчет
                if ($oStudentDiscipline->test1_result !== null && $oStudentDiscipline->test_result !== null) {
                    $oStudentDiscipline->calculateFinalResult();
                }

                // изменяем статус
                $itemSTCP->status = SyllabusTaskCoursePay::STATUS_OK;
                $itemSTCP->save();

                unset($oStudentDiscipline);

            }


        } else {

            Log::info('FIND: 0');

        }
        unset($oSyllabusTaskCoursePay);

        Log::info('--RUN SRO Pay Courses-- END ' . date('Y-m-d H:i:s'));

    }
}
