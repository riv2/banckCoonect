<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Click;
use App\Models\Link;
use App\Models\Pp;
use Faker\Generator as Faker;

$factory->define(Click::class, function (Faker $faker) {
    try {
        $pp = Pp::query()->get()->random();
        $partner = $pp->users->random();
    } catch (Throwable $th) {
    }
    return [
        'pp_id' => $pp->id,
        'partner_id' => $partner->id,
        'link_id' => Link::query()->get()->random()->id,
        'client_id' => rand(256, 100000),
        'click_id' => rand(256, 100000),
        'web_id' => rand(256, 100000),
        'pixel_log_id' => rand(256, 100000),
    ];
});
