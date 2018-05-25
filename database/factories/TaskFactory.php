<?php

use Faker\Generator as Faker;

$factory->define(App\Task::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'description' => $faker->words,
        'due_date' => $faker->date,
    ];
});
