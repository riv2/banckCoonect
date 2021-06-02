<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Lists\PpOnboardingList;
use App\Models\Pp;
use App\User;
use Faker\Generator as Faker;

$factory->define(Pp::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create(['role' => 'advertiser'])->id,
        'tech_domain' => $faker->unique()->slug(1, false) . '.' . config('app.domain'),
        'prod_domain' => $faker->unique()->slug(1, false) . '.' . config('app.domain'),
        'short_name' => $faker->words(2, true),
        'long_name' => $faker->words(4, true),
        'onboarding_status' => $faker->randomElement(array_keys(PpOnboardingList::getList())),
        'company_url' => $faker->url,
        'pp_target' => $faker->randomElement(['lead', 'products']),
        'currency' => $faker->randomElement(['RUB', 'RUB', 'RUB', 'RUB', 'RUB', 'RUB', 'RUB', 'RUB', 'USD']),
        'logo' => '/storage/logo/logo_1.png',
        'branch' => null,
        'color1' => null,
        'color2' => null,
        'color3' => null,
        'color4' => null,
        'lang' => $faker->randomElement([['ru' => true, 'en' => true, 'es' => true], ['ru' => true, 'en' => true, 'es' => false]]),
        'tariff' => $faker->randomElement(['free', 'start', 'professional']),
        'status' => $faker->randomElement(['active', 'banned', 'stopped']),
        'demo_ends_at' => $faker->dateTimeBetween('-1 days', '+60 days'),
        'comment' => '',
    ];
});
