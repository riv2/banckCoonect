<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Offer;
use App\Models\OfferMaterial;
use Faker\Generator as Faker;

$factory->define(OfferMaterial::class, function (Faker $faker) {
    try {
        $offer = Offer::all()->random();
    } catch (Throwable $th) {
    }
    return [
        'offer_id' => $offer->id ?? null,
        'name' => $faker->words(12, true),
        'material_type' => $faker->randomElement(['landing', 'xmlfeed', 'banner']),
        'material_params' => ["link" => $faker->url],
        'material_files' => ['https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Aristotle_Altemps_Inv8575.jpg/1200px-Aristotle_Altemps_Inv8575.jpg'],
    ];
});
