<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Mark;
use App\Device;
use Faker\Generator as Faker;

$factory->define(Device::class, function (Faker $faker) {
    return [
        "name" => $faker->word,
        "mark_id" => function(){return Mark::all()->random();}
    ];
});
