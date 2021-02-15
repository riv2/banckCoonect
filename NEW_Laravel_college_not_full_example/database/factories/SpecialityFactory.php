<?php

use App\Speciality;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    Speciality::class,
    function (Faker $faker) {
        return [
            'trend_id' => $faker->randomNumber(),
            'code_char' => array_random(['m', 'b']),
            'code' => rand(5,7),
            'year' => rand(date('Y') - 5, date('Y')),
            'name' => $faker->name,
            'name_en' => $faker->name,
            'name_kz' => $faker->name,
        ];
    }
);

$factory->state(Speciality::class, '2018', function() {
    return [
        'year' => 2018
    ];
});

$factory->state(Speciality::class, '2019', function() {
    return [
        'year' => 2019
    ];
});
