<?php

use Illuminate\Database\Seeder;

class StudyGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profileList = \App\Profiles::select('team')->groupBy('team')->get();

        foreach ($profileList as $profile)
        {
            if($profile->team)
            {
                $studyGroup = new \App\StudyGroup();
                $studyGroup->name = $profile->team;
                $studyGroup->save();
            }
        }
    }
}
