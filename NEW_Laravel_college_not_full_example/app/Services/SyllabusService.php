<?php
/**
 * User: dadicc
 * Date: 16.07.19
 * Time: 14:07
 */

namespace  App\Services;

use App\{Discipline,Syllabus};
use Illuminate\Support\Facades\Log;

class SyllabusService
{

    /**
     * recalculation syllabus id
     * @param $iDisciplineId
     * @return
     */
    public static function recalculationSyllabusStatus( $iDisciplineId )
    {

        $oDiscipline = Discipline::where('id',$iDisciplineId)->first();
        if( !empty($oDiscipline) )
        {
            // init
            $aLangUnique = [];
            $iSyllabusSum = 0;
            $iKreditSum = 0;

            $oSyllabus = Syllabus::
            where('discipline_id',$iDisciplineId)->
            whereNotNull('language')->
            whereNull('deleted_at')->
            get();
            if( !empty($oSyllabus) && (count($oSyllabus) > 0) )
            {
                foreach( $oSyllabus as $oItem )
                {
                    // find unique lang syllabus
                    if( in_array($oItem->language,$aLangUnique) === false )
                    {
                        $aLangUnique[] = $oItem->language;
                    }

                    // find syllabus sum
                    $iSyllabusSum += intval( $oItem->contact_hours );
                    $iSyllabusSum += intval( $oItem->self_hours );
                    $iSyllabusSum += intval( $oItem->self_with_teacher_hours );
                    $iSyllabusSum += intval( $oItem->sro_hours );
                    $iSyllabusSum += intval( $oItem->srop_hours );
                }
            }
            unset($oSyllabus);

            // find discipline kredit sum
            $iKreditSum = intval( 30 * count($aLangUnique) * $oDiscipline->ects );

            if( ($iSyllabusSum > 0) && ($iKreditSum == $iSyllabusSum) )
            {
                $oDiscipline->recalculation_status = Discipline::RECALCULATION_STATUS_OK;
            } else {
                $oDiscipline->recalculation_status = Discipline::RECALCULATION_STATUS_ERROR;
            }
            $oDiscipline->save();

        }
        unset($oDiscipline);

    }


}