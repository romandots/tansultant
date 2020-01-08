<?php
/**
 * File: StudentFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

/* @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Student::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'name' => $faker->name,
        'card_number' => $faker->unique()->numerify('####'),
        'status' => $faker->randomElement(\App\Models\Student::STATUSES),
        'person_id' => \uuid(),
        'customer_id' => \uuid(),
        'seen_at' => \Carbon\Carbon::now(),
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now()
    ];
});
