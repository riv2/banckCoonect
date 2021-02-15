<?php

use App\{Discipline,Syllabus,SyllabusTask};
use Illuminate\Database\Seeder;

class InitSyllabusTaskData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $oSyllabus = Syllabus::
        whereNull('deleted_at')->
        get();

        if( !empty($oSyllabus) && (count($oSyllabus) > 0) )
        {

            foreach( $oSyllabus as $itemS )
            {
                $oSyllabusTask = SyllabusTask::
                where('syllabus_id',$itemS->id)->
                whereNull('deleted_at')->
                get();


                if( !empty($oSyllabusTask) && (count($oSyllabusTask) > 0) )
                {
                    foreach( $oSyllabusTask as $itemST )
                    {

                        $itemST->discipline_id = $itemS->discipline_id;
                        $itemST->language      = $itemS->language;
                        $itemST->save();

                    }

                }
            }


        }


    }
}
