<?php

use App\DisciplinePayCancel;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    DisciplinePayCancel::class,
    function (Faker $faker) {
        return [
            'discipline_id' => \App\Discipline::getRandomId(),
            'user_id' => \App\User::getRandomStudentId(),
            'admin_id' => \App\User::getRandomTeacherId(),
            'status' => array_random(['new','approve','decline']),
            'executed_1c' => $faker->randomNumber(),
            'executed_miras' => $faker->randomNumber(),
            'decline_reason' => 'UNIT_TEST' . $faker->word,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
);
