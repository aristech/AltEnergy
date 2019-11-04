<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Damage;
use App\DamageType;
use App\Client;
use App\Device;
use App\UsersRoles;
use Faker\Generator as Faker;

$date ="";



$factory->define(Damage::class, function (Faker $faker) {
    return
    [
        "damage_type_id" => function(){return DamageType::all()->random(); },
        "damage_comments" => $faker->text,
        "cost" => $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 10000),
        "guarantee" => $faker->boolean,
        "status" => "Μη Ολοκληρωμένη",
        "appointment_pending" => $faker->boolean,
        "technician_left" => $faker->boolean,
        "technician_arrived" => $faker->boolean,
        "appointment_completed" => $faker->boolean,
        "appointment_needed" => $faker->boolean,
        "supplement_pending" => $faker->boolean,
        "damage_fixed" => 0,
        "completed_no_transaction" => 0,
        "client_id" => function(){ return Client::where('id','!=',null)->inRandomOrder()->first()['id']; },
        "manufacturer_id" => 2,
        "mark_id" => 2,
        "device_id" => function()
        {
            return  Device::where('mark_id', 2)->inRandomOrder()->first()['id'];
        },
        "supplement" => $faker->word,
        "comments" => $faker->text,
        "techs" => function()
        {
            return UsersRoles::where("role_id",3)->inRandomOrder()->first()['user_id'];
        }
    ];
});




