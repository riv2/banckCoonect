<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\User;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    $user = User::query()->where('role', '=', 'partner')->get()->random();
    $link = $user->links->random();
    return [
        'order_id' => 'tst-' . $faker->randomNumber(6),
        'offer_id' => $link->offer_id,
        'datetime' => $faker->dateTimeBetween('-7days'),
        'partner_id' => $user->id,
        'pp_id' => $user->pp_id,
        'category_id' => null,
        'landing_id' => null,
        'link_id' => $link->id,
        'click_id' => 'clid-' . $faker->word,
        'web_id' => 'wbid-' . $faker->randomNumber,
        'client_id' => null,
        'pixel_id' => null,
        'business_unit_id' => null,
        'fee' => null,
        'fee_id' => null,
        'fee_advert' => null,
        'model' => null,
        'gross_amount' => $faker->numberBetween(1000, 10000),
        'amount' => null,
        'amount_advert' => null,
        'cnt_products' => 0,
        'reestr_id' => null,
        'status' => $faker->randomElement(['new']),
        'status_cnt' => null,
        'status_datetime' => null,
        'last_updated' => null,
        'wholesale' => $faker->boolean(2),
        'created_at' => now(),
    ];
});
