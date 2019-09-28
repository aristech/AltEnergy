<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Manufacturer;
use App\Mark;
use Faker\Generator as Faker;

$factory->define(Mark::class, function (Faker $faker) {
    return [
        "name" => $faker->word,
        "manufacturer_id" => function(){ return Manufacturer::all()->random();}
    ];
});
