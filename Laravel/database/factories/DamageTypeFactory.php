<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\DamageType;
use Faker\Generator as Faker;

$factory->define(DamageType::class, function (Faker $faker) {
    return
    [
        "name" => $faker->word
    ];
});
