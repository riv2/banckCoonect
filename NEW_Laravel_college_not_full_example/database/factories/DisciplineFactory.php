<?php

use App\Discipline;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    Discipline::class,
    function (Faker $faker) {
        return [
            'sector_id' => $faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => 'UNIT_TEST' . $faker->name,
            'credits' => rand(1,10),
            'kz' => $faker->randomNumber(),
            'ru' => $faker->randomNumber(),
            'en' => $faker->randomNumber(),
            'dependence' => $faker->word,
            'dependence2' => $faker->word,
            'dependence3' => $faker->word,
            'dependence4' => $faker->word,
            'dependence5' => $faker->word,
            'ects' => rand(1, 10),
            'tests_lang_invert' => $faker->boolean,
            'verbal_sro' => $faker->boolean,
            'control_form' => array_random(['test','write','report','score','traditional','protect']),
            'is_practice' => $faker->boolean,
            'has_diplomawork' => $faker->boolean,
            'practise_1sem_control_start' => '',
            'practise_1sem_control_end' => '',
            'practise_2sem_control_start' => '',
            'practise_2sem_control_end' => '',
            'language_level' => 0,
        ];
    }
);
