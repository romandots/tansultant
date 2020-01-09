<?php
/**
 * File: CourseFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Course;
use Faker\Generator as Faker;

$factory->define(Course::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'name' => $faker->name,
        'status' => Course::STATUS_ACTIVE,
        'summary' => $faker->sentence,
        'description' => $faker->text,
        'picture' => $faker->imageUrl(),
        'picture_thumb' => $faker->imageUrl(),
        'age_restrictions_from' => null,
        'age_restrictions_to' => null,
        'starts_at' => \Carbon\Carbon::now(),
        'ends_at' => \Carbon\Carbon::now()->addYear(),
        'instructor_id' => \uuid(),
    ];
});
