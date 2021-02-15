<?php

use App\QuizAnswer;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    QuizAnswer::class,
    function (Faker $faker) {
        $correct = $faker->boolean;

        return [
            'answer' => 'UNIT_TEST. '. $faker->word,
            'points' => $correct ? rand(1, 5) : 0,
            'correct' => $correct,
            'img' => $faker->word,
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ];
    }
);

$factory->state(QuizAnswer::class,'correct', function($faker) {
    return [
        'correct' => true,
        'points' => rand(1, 5)
    ];
});

$factory->state(QuizAnswer::class,'wrong', function($faker) {
    return [
        'correct' => false,
        'points' => 0
    ];
});