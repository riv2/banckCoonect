<?php

namespace App\Console\Commands;

use App\{
    StudentDiscipline,
    SyllabusTask
};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Log};

class GetDisciplinesIdFromSRONoResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sro:disciplines:noresult';

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

        $oSyllabusTask = SyllabusTask::
        with('discipline')->
        with('questions')->
        with('questions.answer')->
        whereHas('taskResultAll')->
        whereNull('deleted_at')->
        get();

        $aData = [];
        if( !empty($oSyllabusTask) && (count($oSyllabusTask) > 0) )
        {
            foreach( $oSyllabusTask as $itemT )
            {

                // TODO надо добавить результат
                $bFlag = false;
                if( !empty($itemT->questions) && (count($itemT->questions) > 0) )
                {

                    $iQPoints = 0;
                    // проверяем баллы заданий
                    foreach( $itemT->questions as $itemQuestion )
                    {
                        $iQPoints += $itemQuestion->points;
                    }
                    if( $iQPoints > 40 )
                    {
                        $bFlag = true;
                    }

                    // если баллы заданий в норме, проверяем баллы ответов
                    if( ($iQPoints < 40) && !$bFlag )
                    {

                        $iAPoints = 0;
                        foreach( $itemT->questions as $itemQuestions )
                        {
                            if( !empty($itemQuestions->answer) && (count($itemQuestions->answer) > 0) )
                            {
                                foreach( $itemQuestions->answer as $itemAnswer )
                                {
                                    $iAPoints += $itemAnswer->points;
                                }
                            }
                        }
                        if( $iAPoints > 40 )
                        {
                            $bFlag = true;
                        }
                    }

                }


                if( $bFlag && empty($aData[$itemT->discipline_id]) )
                {
                    $sTaskText = !empty($itemT->text_data) ? substr($itemT->text_data,0,50) : '';
                    $aData[$itemT->discipline_id] = 'discipline_id: ' . $itemT->discipline_id . ' | discipline_name: ' . $itemT->discipline->name . ' | lang: ' . $itemT->language . ' | task_id: ' . $itemT->id . ' | task_name: ' . $sTaskText;
                }
            }
        }

        // если есть результаты
        if( count($aData) > 0 )
        {
            // логируем результаты
            $i = 0;
            foreach( $aData as $itemD )
            {
                Log::info( 'num: ' . ++$i . ' | ' .  $itemD );
            }

        }
        unset($oSyllabusTask,$oSyllabusTask);

    }
}
