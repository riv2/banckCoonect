<?php

use App\QuizQuestion;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    QuizQuestion::class,
    function (Faker $faker) {
        return [
            'discipline_id' => \App\Discipline::getRandomId(),
            'question' => 'UNIT_TEST. '. $faker->word,
            'teacher_id' => \App\User::getRandomTeacherId(),
            'total_points' => rand(1, 5),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
);

