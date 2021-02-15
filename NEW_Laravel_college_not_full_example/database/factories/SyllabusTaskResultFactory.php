<?php

use App\SyllabusTaskResult;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    SyllabusTaskResult::class,
    function (Faker $faker) {
        return [
            'user_id' => \App\User::getRandomStudentId(),
            'syllabus_id' => \App\Syllabus::getRandomId(),
            'payed' => $faker->boolean,
            'value' => rand(0, 100),
            'points' => rand(0, 40),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
            'blur' => $faker->boolean,
            'task_id' => \App\SyllabusTask::getRandomId(),
        ];
    }
);
