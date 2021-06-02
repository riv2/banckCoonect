<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Link;
use App\Models\Pp;
use Faker\Generator as Faker;

$factory->define(Link::class, function (Faker $faker) {
    try {
        $pp = Pp::get()->random();
        $partner = $pp->users->random();
        $offer = $pp->offers->random();
    } catch (\Throwable $th) {
    }
    return [
        'pp_id' => $pp->id,
        'partner_id' => $partner->id,
        'link_name' => $faker->monthName(),
        'link' => 'https://ya.ru/?q=' . rand(0, 100000),
        'link_source' => null,
        'offer_id' => $offer->id,
        'offer_materials_id' => null,
        'status' => \App\Helpers\ArrayHelper::getRandomValue(["ACTIVE", "ACTIVE", "ACTIVE", "ACTIVE", "ACTIVE", "DELETED"]),
    ];
});
