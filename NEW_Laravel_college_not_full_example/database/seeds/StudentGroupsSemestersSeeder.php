<?php

use App\Profiles;
use App\StudentGroupsSemesters;
use Illuminate\Database\Seeder;

class StudentGroupsSemestersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Profiles::select(['id', 'user_id', 'study_group_id'])
            ->whereNotNull('study_group_id')
            ->where('study_group_id', '!=', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('student_groups_semesters')
                      ->whereRaw('student_groups_semesters.user_id = profiles.user_id');
            })
            ->chunk(100, function ($profiles) {
                foreach ($profiles as $profile) {
                    /** @var Profiles $profile */
                    StudentGroupsSemesters::add($profile->user_id, $profile->study_group_id, '2019.1');
                }
            });
    }
}
