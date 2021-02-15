<?php

use App\BcApplications;
use App\Profiles;
use App\SpecialitySemester;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    SpecialitySemester::class,
    function (Faker $faker) {
        return [
            'speciality_id' => \App\Speciality::getRandomId(),
            'study_form' => array_random([
                Profiles::EDUCATION_STUDY_FORM_ONLINE,
                Profiles::EDUCATION_STUDY_FORM_EVENING,
                Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL,
                Profiles::EDUCATION_STUDY_FORM_FULLTIME
            ]),
            'base_education' => array_random([
                BcApplications::EDUCATION_HIGH_SCHOOL,
                BcApplications::EDUCATION_HIGHER,
                BcApplications::EDUCATION_BACHELOR,
                BcApplications::EDUCATION_VOCATIONAL_EDUCATION
            ]),
            'semester' => rand(1, 3),
            'type' => array_random(\App\Semester::$types),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::tomorrow(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
);

$factory->state(SpecialitySemester::class, 'test1', function (Faker $faker) {
    return ['type' => \App\Semester::TYPE_TEST1];
});

$factory->state(SpecialitySemester::class, 'exam', function (Faker $faker) {
    return ['type' => \App\Semester::TYPE_EXAM];
});

$factory->state(SpecialitySemester::class, 'sro', function (Faker $faker) {
    return ['type' => \App\Semester::TYPE_SRO];
});
