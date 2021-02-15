<?php

use App\Syllabus;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    Syllabus::class,
    function (Faker $faker) {
        return [
            'discipline_id' => \App\Discipline::getRandomId(),
            'language' => array_random(['ru', 'kz']),
            'theme_number' => $faker->word,
            'theme_name' => 'UNIT_TEST' . $faker->name,
            'literature' => $faker->word,
            'contact_hours' => rand(1,100),
            'self_hours' => rand(1,100),
            'self_with_teacher_hours' => rand(1,100),
        ];
    }
);
