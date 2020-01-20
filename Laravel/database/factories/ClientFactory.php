<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use Faker\Generator as Faker;
use App\Http\CustomClasses\v1\Greeklish;

$factory->define(Client::class, function (Faker $faker) {
    return [
        "lastname" => $faker->lastName,
        "firstname" => $faker->firstNameMale,
        "telephone" => $faker->phoneNumber,
        "telephone2" => $faker->phoneNumber,
        "mobile" => $faker->phoneNumber,
        "address" => $faker->address,
        "zipcode" => $faker->postcode,
        "location" => $faker->city,
        "email" => $faker->email,
        "level" => $faker->word
    ];
});
