<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence,
        'due_by' => now()->addMinutes($faker->numberBetween(10, 10000)),
        'completed' => false
    ];
});
