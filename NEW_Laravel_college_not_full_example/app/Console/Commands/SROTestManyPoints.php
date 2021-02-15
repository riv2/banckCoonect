<?php

namespace App\Console\Commands;

use App\{
    Discipline,
    Profiles,
    SyllabusTask
};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Log};

class SROTestManyPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sro:test:manypoints';

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

        $aLangList = [
            Profiles::EDUCATION_LANG_RU,
            Profiles::EDUCATION_LANG_KZ,
            Profiles::EDUCATION_LANG_EN
        ];

        // init
        $aData = [];

        // получаем список дисциплин
        $oDiscipline = Discipline::get();
        if( !empty($oDiscipline) && ( count($oDiscipline) > 0 ) )
        {
             foreach( $oDiscipline as $oItemD )
             {

                 // проходка по языкам
                 foreach( $aLangList as $sItemL )
                 {

                     // init
                     $bFlag   = false;
                     $iPoints = 0;

                     // получаем список СРО по языку и дисциплине
                     $oSyllabusTask = SyllabusTask::
                     with('questions')->
                     with('questions.answer')->
                     where('discipline_id',$oItemD->id)->
                     where('language',$sItemL)->
                     whereNull('deleted_at')->
                     get();


                     if( !empty($oSyllabusTask) && (count($oSyllabusTask) > 0) )
                     {
                         foreach( $oSyllabusTask as $oItemST )
                         {

                             $iPoints += $oItemST->points;

                             // анализ вопросов и ответов
                             if( !empty($oItemST->questions) && (count($oItemST->questions) > 0) )
                             {

                                 $iQPoints = 0;
                                 // проверяем баллы вопросов
                                 foreach( $oItemST->questions as $itemQuestion )
                                 {
                                     $iQPoints += $itemQuestion->points;
                                 }
                                 if( $iQPoints > 40 )
                                 {
                                     $bFlag = true;
                                 }

                                 // если баллы вопросов в норме, проверяем баллы ответов
                                 if( ($iQPoints < 40) && !$bFlag )
                                 {

                                     $iAPoints = 0;
                                     foreach( $oItemST->questions as $itemQuestions )
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

                             if( $bFlag && empty($aData[$oItemD->id]) )
                             {
                                 $aData[$oItemD->id] = 'discipline_id: ' . $oItemD->id . ' | discipline_name: ' . $oItemD->name . ' | lang: ' . $sItemL;
                             }


                         }
                     }
                     unset($oSyllabusTask);

                     // проверяем сумму балов всех заданий СРО
                     if( ($iPoints > 40) &&  empty($aData[$oItemD->id]) )
                     {
                         $aData[$oItemD->id] = 'discipline_id: ' . $oItemD->id . ' | discipline_name: ' . $oItemD->name . ' | lang: ' . $sItemL;
                     }


                 }

             }
        }
        unset($oDiscipline);

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

    }
}
