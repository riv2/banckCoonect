<?php

use Illuminate\Database\Seeder;
use App\Profiles;


class ChangeProfileEducationStudyForm extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $obProfiles = Profiles::where('education_study_form','=','night')->get();
        if( !empty($obProfiles) && (count($obProfiles) > 0) )
        {

            foreach( $obProfiles as $obItem )
            {

                $obItem->education_study_form = Profiles::EDUCATION_STUDY_FORM_EVENING;
                $obItem->save();
            }

        }
        unset($obProfiles);

    }
}
