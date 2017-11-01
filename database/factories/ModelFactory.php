<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Devon\AuthPlus\Group::class, function (Faker\Generator $faker) {
    return [
        'name'        => $faker->colorName,
        'description' => $faker->sentence,
    ];
});

$factory->define(User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

