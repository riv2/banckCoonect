<?php

use App\BcApplications;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    BcApplications::class,
    function (Faker $faker) {
        return [
            'education' => array_random(['vocational_education', 'high_school', 'bachelor', 'higher']),
            'citizenship_id' => 1,
        ];
    }
);

$factory->state(BcApplications::class, BcApplications::EDUCATION_VOCATIONAL_EDUCATION, function () {
    return [
        'education' => BcApplications::EDUCATION_VOCATIONAL_EDUCATION
    ];
});

$factory->state(BcApplications::class, BcApplications::EDUCATION_HIGH_SCHOOL, function () {
    return [
        'education' => BcApplications::EDUCATION_HIGH_SCHOOL
    ];
});

$factory->state(BcApplications::class, BcApplications::EDUCATION_BACHELOR, function () {
    return [
        'education' => BcApplications::EDUCATION_BACHELOR
    ];
});

$factory->state(BcApplications::class, BcApplications::EDUCATION_HIGHER, function () {
    return [
        'education' => BcApplications::EDUCATION_HIGHER
    ];
});