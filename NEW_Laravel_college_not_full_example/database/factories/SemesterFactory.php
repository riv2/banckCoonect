<?php

use App\Semester;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    Semester::class,
    function (Faker $faker) {
        return [
            'study_form' => array_random([
                \App\Profiles::EDUCATION_STUDY_FORM_FULLTIME,
                \App\Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL,
                \App\Profiles::EDUCATION_STUDY_FORM_EVENING,
                \App\Profiles::EDUCATION_STUDY_FORM_ONLINE
            ]),
            'number' => rand(1, 3),
            'type' => array_random(['study','plan_approval','buying','buy_cancel','syllabuses','test1','test1_retake','sro','sro_retake','exam','exam_retake']),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
);

$factory->state(Semester::class, 'test1_yesterday_ended', function ($faker) {
    return [
        'type' => 'test1',
        'start_date' => Carbon::now()->subDay(30),
        'end_date' => Carbon::now()->subDay(),
    ];
});

$factory->state(Semester::class, 'fulltime_test1_yesterday_ended', function ($faker) {
    return [
        'study_form' => 'fulltime',
        'type' => 'test1',
        'start_date' => Carbon::now()->subDay(30),
        'end_date' => Carbon::now()->subDay(),
    ];
});

$factory->state(Semester::class, 'exam_yesterday_ended', function ($faker) {
    return [
        'type' => 'exam',
        'start_date' => Carbon::now()->subDay(30),
        'end_date' => Carbon::now()->subDay(),
    ];
});

$factory->state(Semester::class, 'test1', function ($faker) {
    return [
        'type' => Semester::TYPE_TEST1
    ];
});

$factory->state(Semester::class, 'exam', function ($faker) {
    return [
        'type' => Semester::TYPE_EXAM
    ];
});
