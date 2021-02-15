<?php

use App\MgApplications;
use Illuminate\Database\Seeder;

class ChangeMasterEducationToHigher extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $oMgApplications = MgApplications::get();
        if( !empty($oMgApplications) && ( count($oMgApplications) > 0 ) )
        {
            foreach($oMgApplications as $oItem)
            {
                $oItem->education = MgApplications::EDUCATION_HIGHER;
                $oItem->save();
            }
        }
        unset($oMgApplications);

    }
}
