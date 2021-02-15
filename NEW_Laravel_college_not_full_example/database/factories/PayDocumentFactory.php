<?php

use App\PayDocument;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    PayDocument::class,
    function (Faker $faker) {
        return [
            'order_id' => rand(10000, 10000000),
            'user_id' => \App\User::getRandomStudentId(),
            'student_discipline_id' => $faker->randomNumber(),
            'amount' => (float)rand(0, 10000),
            'balance_before' => $faker->randomFloat(2),
            'credits' => rand(1, 5),
            'status' => array_random([
                PayDocument::STATUS_PROCESS,
                PayDocument::STATUS_SUCCESS,
                PayDocument::STATUS_FAIL,
                PayDocument::STATUS_CANCEL
            ]),
            'type' => array_random([
                PayDocument::TYPE_DISCIPLINE,
                PayDocument::TYPE_LECTURE,
                PayDocument::TYPE_LECTURE_ROOM,
                PayDocument::TYPE_TEST,
                PayDocument::TYPE_RETAKE_TEST,
                PayDocument::TYPE_RETAKE_EXAM,
                PayDocument::TYPE_RETAKE_KGE,
                PayDocument::TYPE_REGISTRATION_FEE,
                PayDocument::TYPE_TO_BALANCE,
                PayDocument::TYPE_WIFI,
                PayDocument::TYPE_REGISTRATION,
            ]),
            'complete_pay' => rand(0, 1),
            'hash' => 'UNIT_TEST' . $faker->word,
            'created_at' => Carbon::now(),
        ];
    }
);
