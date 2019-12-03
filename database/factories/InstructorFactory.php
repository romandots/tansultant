<?php
/**
 * File: InstructorFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Instructor::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'name' => $faker->name,
        'description' => $faker->sentence,
        'picture' => $faker->imageUrl(),
        'display' => true,
        'status' => $faker->randomElement(\App\Models\Instructor::STATUSES),
        'person_id' => \uuid(),
        'seen_at' => \Carbon\Carbon::now(),
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now()
    ];
});
