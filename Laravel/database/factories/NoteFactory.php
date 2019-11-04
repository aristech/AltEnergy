<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Note;
use App\User;
use Faker\Generator as Faker;

$factory->define(Note::class, function (Faker $faker) {
    return [

        "user_id" => function(){return User::all()->random();},
        "updated_by" =>  function(){return User::all()->random();},
        "title" => $faker->word,
        "importance" => function(){return rand(0,3);},
        "all_day" => $faker->boolean,
        "description" => $faker->text
        // "dateTime_start" => function(Faker $faker){
        //     $date = $faker->iso8601($max = '+1 month');
        //     $date = substr_replace($date ,"", -1);
        //     $date = str_replace('+','.',$date);
        //     $date = $date.'Z';
        //     return $date;
        // }




    ];
});
