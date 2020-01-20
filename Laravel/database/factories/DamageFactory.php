<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Damage;
use App\DamageType;
use App\Client;
use App\Device;
use App\UsersRoles;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Damage::class, function (Faker $faker) {
    $time_start = $faker->dateTimeBetween('now', '+1 week');
    $carbon_date = Carbon::parse($time_start);
    $time_end = $carbon_date->addHours(2);

    return
        [
            "damage_type_id" => function () {
                return DamageType::all()->random();
            },
            "damage_comments" => $faker->text,
            "cost" => $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 10000),
            "guarantee" => $faker->boolean,
            "status" => "Μη Ολοκληρωμένη",
            "appointment_pending" => false,
            "technician_left" => false,
            "technician_arrived" => false,
            "appointment_completed" => false,
            "appointment_needed" => false,
            "supplement_pending" => false,
            "damage_fixed" => false,
            "completed_no_transaction" => false,
            "client_id" => function () {
                return Client::all()->random();
            },
            "manufacturer_id" => 2,
            "mark_id" => 3,
            "device_id" => function () {
                return  Device::where('mark_id', 3)->inRandomOrder()->first()->id;
            },
            "supplement" => $faker->word,
            "comments" => $faker->text,
            "techs" => function () {
                return UsersRoles::where("role_id", 3)->inRandomOrder()->first()['id'];
            },
            "appointment_start" => $time_start,
            "appointment_end" => $time_end

        ];
});
