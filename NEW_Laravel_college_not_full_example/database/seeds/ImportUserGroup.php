<?php

use App\{Profiles,User};
use Illuminate\Support\Facades\{File,Log};
use Illuminate\Database\Seeder;

class ImportUserGroup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        foreach(file(storage_path('import_user_group_040919.csv')) as $iKey => $sLine)
        {

            // continue 1 line - titles
            if( $iKey == 0 ) { continue; }

            // INFO //
            /*
            0 - id
            1 - fio
            2 - spec
            3 - spec1
            4 - group
            5 - education
            6 - lang
            */

            // parse line
            $aLine = explode(';',$sLine);
            if( !empty($aLine) && ( count($aLine) > 5 ) )
            {

                $oProfiles = Profiles::
                select([
                    'id',
                    'user_id',
                    DB::RAW('LOWER(fio) as fio')
                ])->
                where('fio', 'like', '%' . strtolower($aLine[1]) . '%')->
                orWhere('user_id',$aLine[0])->
                //whereRaw('LOWER(fio) like '.strtolower($aLine[1]))->
                first();
                if( !empty($oProfiles) )
                {
                    $oProfiles->team = $aLine[4];
                    $oProfiles->save();
                }
                unset($oProfiles);

            }

        }


    }
}
