<?php

use App\{BcApplications,MgApplications,Profiles,User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{Log};

class ProfileRegistrationStepFinish extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // get data
        $oUser = Profiles::
        with('User')->
        where('registration_step','!=','finish')->
        whereHas('User')->
        get();

        if( !empty($oUser) && ( count($oUser) > 0 ) )
        {

            foreach( $oUser as $oItemU )
            {

                $bIsFullData = true;
                $oApplication = BcApplications::where('user_id',$oItemU->user_id)->first();
                if( empty($oApplication) )
                {
                    $oApplication = MgApplications::where('user_id',$oItemU->user_id)->first();
                }

                // 3 family status
                /*
                if( empty($oItemU->family_status) )
                {

                    $bIsFullData = false;
                    continue;
                }
                */

                // 5 choose education
                if( empty($oApplication) )
                {

                    $bIsFullData = false;
                    continue;
                }

                // 6 choose speciality
                if( empty($oItemU->education_speciality_id) )
                {

                    $bIsFullData = false;
                    continue;
                }

                // 7 choose education lang
                if(  empty($oItemU->education_lang) )
                {

                    $bIsFullData = false;
                    continue;
                }

                // 8 choose education study form
                if( empty($oItemU->education_study_form) )
                {

                    $bIsFullData = false;
                    continue;
                }

                // 9 adress
                if( empty($oApplication->street) )
                {

                    $bIsFullData = false;
                    continue;
                }

                // 10 education
                if( empty($oApplication->numeducation) && empty($oApplication->sereducation) )
                {

                    $bIsFullData = false;
                    continue;
                }


                if( !empty($bIsFullData) )
                {
                    // change registration_step to finish
                    $oItemU->registration_step = 'finish';
                    $oItemU->save();
                }


            }



        }
        unset($oProfiles);


    }
}
