<?php

use App\Profiles;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    Profiles::class,
    function (Faker $faker) {
        return [
            'status' => array_random(['moderation', 'active', 'disallow', 'block']),
            'education_status' => array_random(['matriculant','student','send_down','academic_leave','pregraduate','graduate','temp_suspended']),
            'check_level' => array_random(['inspection','or_cabinet']),
            'category' => array_random(['matriculant','standart','standart_recount','transit','trajectory_change','retake_ent','transfer']),
            'iin' => $faker->numerify('############'),
            'fio' => $faker->firstName .' '. $faker->lastName,
            'education_lang' => array_random(['ru', 'kz']),
            'education_speciality_id' => \App\Speciality::getRandomId(),
            'education_study_form' => array_random(['fulltime', 'online', 'evening', 'extramural']),
            'course' => rand(1,5),
//            'team' => $faker->word,
//            'study_group_id' => $faker->randomNumber(),
            'is_transfer' => 0,
            'elective_speciality_id' => null,
            'semester_credits_limit' => null,
            'buying_allow' => rand(0,1),
            'remote_exam_qr' => 0
        ];
    }
);

$factory->state(Profiles::class, 'active', function () {
    return [
        'status' => Profiles::STATUS_ACTIVE,
        'education_status' => Profiles::EDUCATION_STATUS_STUDENT,
        'check_level' => Profiles::CHECK_LEVEL_INSPECTION,
        'category' => Profiles::CATEGORY_STANDART,
    ];
});

$factory->state(Profiles::class, 'fulltime', function () {
    return [
        'education_study_form' => Profiles::EDUCATION_STUDY_FORM_FULLTIME,
    ];
});

$factory->state(Profiles::class, 'online', function () {
    return [
        'education_study_form' => Profiles::EDUCATION_STUDY_FORM_ONLINE,
    ];
});

$factory->state(Profiles::class, 'admission2019', function () {
    return [
        'education_speciality_id' => \App\Speciality::getRandomId(2019),
    ];
});

$factory->state(Profiles::class, 'admission2018', function () {
    return [
        'education_speciality_id' => \App\Speciality::getRandomId(2018),
    ];
});


