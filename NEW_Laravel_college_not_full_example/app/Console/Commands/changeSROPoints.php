<?php

namespace App\Console\Commands;

use App\{Discipline,SyllabusTask};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Log};

class changeSROPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:sro:point';

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

        $oDiscipline = Discipline::
        whereNull('deleted_at')->
        get();


        if( !empty($oDiscipline) )
        {
            foreach( $oDiscipline as $itemD )
            {

                $oSyllabusTask = SyllabusTask::
                with('questions')->
                where('discipline_id',$itemD->id)->
                whereNull('deleted_at')->
                get();


                if( !empty($oSyllabusTask) && (count($oSyllabusTask) > 0) )
                {

                    $iSum = 0;
                    foreach( $oSyllabusTask as $itemST )
                    {
                        $iSum += intval($itemST->points);
                    }

                    // если больше 20 баллов половиним и меняем в вопросах и ответах
                    if( $iSum > 20 )
                    {

                        foreach( $oSyllabusTask as $itemST )
                        {

                            // вопросы
                            if( !empty($itemST->questions) )
                            {
                                foreach( $itemST->questions as $questionItem )
                                {

                                    // ответы
                                    if( !empty($questionItem->answer) )
                                    {
                                        foreach( $questionItem->answer as $answerItem )
                                        {

                                            // меняем баллы в ответе
                                            if( $answerItem->points > 0 )
                                            {
                                                $answerItem->points = intval( $answerItem->points / 2 );
                                                $answerItem->save();
                                            }
                                        }
                                    }

                                    // меняем баллы в вопросе
                                    if( $questionItem->points > 0 )
                                    {
                                        $questionItem->points = intval($questionItem->points / 2);
                                        $questionItem->save();
                                    }
                                }
                            }


                            // меняем баллы в задании
                            if( $itemST->points > 0 )
                            {
                                $itemST->points = intval($itemST->points / 2);
                                $itemST->save();
                            }
                        }

                    }


                    //Log::info('questions: ' . var_export($itemST->questions,true));


                }

                unset($oSyllabusTask);

            }
        }

    }

}
