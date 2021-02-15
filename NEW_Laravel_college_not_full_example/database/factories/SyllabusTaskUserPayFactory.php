<?php

use App\SyllabusTaskUserPay;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    SyllabusTaskUserPay::class,
    function (Faker $faker) {
        return [
            'task_id' => \App\SyllabusTask::getRandomId(),
            'user_id' => User::getRandomStudentId(),
            'active' => array_random([SyllabusTaskUserPay::STATUS_ACTIVE, SyllabusTaskUserPay::STATUS_INACTIVE]),
            'payed' => array_random([SyllabusTaskUserPay::STATUS_PAYED_ACTIVE, SyllabusTaskUserPay::STATUS_PAYED_INACTIVE]),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
);
