<?php

use App\{Discipline};
use App\Services\{SyllabusService};
use Illuminate\Database\Seeder;

class SyllabusRecalculationStatusInit extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $oDiscipline = Discipline::get();
        if( !empty($oDiscipline) && (count($oDiscipline) > 0) )
        {
            foreach( $oDiscipline as $oItem )
            {
                SyllabusService::recalculationSyllabusStatus( $oItem->id );
            }
        }
        unset($oDiscipline);

    }

}
