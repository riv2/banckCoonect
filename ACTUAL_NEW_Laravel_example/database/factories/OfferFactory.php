<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Offer;
use App\Models\Pp;
use Faker\Generator as Faker;

$factory->define(Offer::class, function (Faker $faker) {
    $pp = Pp::get()->random();
    return [
        'user_id' => $pp->user_id,
        'pp_id' => $pp->id,
        'offer_name' => $faker->company(),
        'model' => $faker->randomElement(['new', 'sale']),
        'fee_type' => $faker->randomElement(['fix', 'share']),
//        'fee' => $faker->randomElement([10, 20, 30]),
        'info_link' => $faker->url(),
        'description' => $faker->paragraph(),
    ];
});
