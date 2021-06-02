<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Pp;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    try {
        $pp = Pp::all()->random();
    } catch (\Throwable $th) {
    }
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'role' => 'partner',
        'pp_id' => $pp->id ?? null,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'status' => 0,
        'need_api' => 0,
        'remember_token' => Str::random(10),
        'auth_token' => Str::random(10),
    ];
});
