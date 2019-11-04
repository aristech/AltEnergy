<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [
        "lastname" => $faker->lastName,
        "firstname" => $faker->firstName($gender = null),
        "afm" => $faker->creditCardNumber,
        "doy" => $faker->creditCardNumber,
        "arithmos_gnostopoihshs" => $faker->creditCardNumber,
        "arithmos_meletis" => $faker->creditCardNumber,
        "arithmos_hkasp" => $faker->creditCardNumber,
        "arithmos_aitisis" => $faker->creditCardNumber,
        "plithos_diamerismaton" => 2,
        "dieuthinsi_paroxis" => $faker->address,
        "kw_oikiako" => rand(1000,3000),
        "kw" => rand(1000,3000),
        "levitas" => $faker->word,
        "telephone" => $faker->phoneNumber,
        "telephone2" => $faker->phoneNumber,
        "mobile" => $faker->phoneNumber,
        "address" =>  $faker->address,
        "zipcode" => rand(10000,50000),
        "location" => $faker->word,
        "level" => 2,
        "foldername" => $faker->unique()->word
    ];
});
