<?php

namespace App\Console\Commands;

use App\{
    StudentDiscipline,
    SyllabusTaskResult
};
use App\Services\{StudentRating,SRORecalculationService};
use Illuminate\Support\Facades\{Log};
use Illuminate\Console\Command;

class SRORecalculatePercent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sro:recalculation:percent';

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

        Log::info('Start command sro:recalculation:percent ' . date('Y-m-d H:i:s'));

        // init
        $iMaxPercent = 100;
        $iMaxPoints  = 20;

        // поиск студентов у которых завышены баллы
        $oStudentDiscipline = StudentDiscipline::
        where('task_result','>',$iMaxPercent)->                                    // %
        orWhere('task_result_points','>',$iMaxPoints)->                            // баллы
        get();

        // если есть результаты
        if( !empty($oStudentDiscipline) && (count($oStudentDiscipline) > 0) )
        {
            Log::info('find records: ' . count($oStudentDiscipline));
            foreach( $oStudentDiscipline as $itemSD )
            {

                // пересчитываем результат
                $iPoint = intval( ($iMaxPercent * $itemSD->task_result_points) / $itemSD->task_result );
                if( $iPoint > $iMaxPoints ){ $iPoint = $iMaxPoints; }
                $itemSD->task_result        = $iMaxPercent;
                $itemSD->task_result_points = $iPoint;
                $itemSD->task_result_letter = StudentRating::getLetter( $iMaxPercent );
                $itemSD->task_date = date('Y-m-d H:i:s');
                $itemSD->task_blur = 0;
                $itemSD->save();

            }
        }
        unset($oStudentDiscipline);


        // запускаем пересчет результатов
        SRORecalculationService::SRORecalculation();

        Log::info('End command sro:recalculation:percent ' . date('Y-m-d H:i:s'));

    }
}
