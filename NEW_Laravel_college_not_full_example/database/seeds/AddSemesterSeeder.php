<?php

use App\Profiles;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use Illuminate\Database\Seeder;

class AddSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Profiles::chunk(500, function($profiles) {
            foreach ($profiles as $profile) {
                /** @var Profiles $profile */
                $specialityId = $profile->education_speciality_id;

                if (empty($specialityId)) {
                    continue;
                }

                $userId = $profile->user_id;

                $studentDisciplines = StudentDiscipline::where('student_id', $userId)->where('semester', null)->get();
                foreach ($studentDisciplines as $studentDiscipline) {
                    /** @var StudentDiscipline $studentDiscipline */
                    $studentDiscipline->semester = SpecialityDiscipline::getSemester($specialityId, $studentDiscipline->discipline_id);
                    $studentDiscipline->save();
                }
            }
        });
    }
}
