<?php

use App\StudentFinanceNomenclature;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    StudentFinanceNomenclature::class,
    function (Faker $faker) {
        return [
            'user_id' => \App\User::getRandomStudentId(),
            'finance_nomenclature_id' => \App\FinanceNomenclature::getRandomId(),
            'student_discipline_id' => \App\StudentDiscipline::getRandomId(),
            'comment' => $faker->word,
            'cost' => $faker->randomNumber(),
            'semester' => rand(1, 8),
            'balance_before' => $faker->randomFloat(2),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
);
