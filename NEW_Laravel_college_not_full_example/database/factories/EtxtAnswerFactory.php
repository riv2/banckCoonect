<?php

use Faker\Generator as Faker;
use Faker\Provider\Lorem;
use App\EtxtAnswer;

$factory->define(EtxtAnswer::class, function (Faker $faker) {
    return [
        'user_id'                  => '123',
        'name'                     => $faker->name,
        'type'                     => 'text',
        'text'                     => $faker->text(),
        'compare_method'           => 'Shingle',
        'num_samples_per_document' => $faker->numberBetween(1, 10),
        'num_samples'              => $faker->numberBetween(1, 50),
        'num_ref_per_sample'       => $faker->numberBetween(1, 10),
        'num_words_i_shingle'      => $faker->numberBetween(1, 10),
        'uniqueness_threshold'     => $faker->numberBetween(1, 100),
        'self_uniq'                => $faker->boolean,
        'ignore_citation'          => $faker->boolean
    ];
});
