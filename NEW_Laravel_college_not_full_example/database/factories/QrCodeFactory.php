<?php

use App\QrCode;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    QrCode::class,
    function (Faker $faker) {
        return [
            'teacher_id' => User::getRandomTeacherId(),
            'discipline_id' => \App\Discipline::getRandomId(),
            'code' => str_random(50),
            'numeric_code' => rand(100000, 999999),
            'meta' => $faker->word,
            'expire_sec' => 15,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
);
