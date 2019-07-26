<?php
/**
 * File: CourseFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Course;
use Faker\Generator as Faker;

$factory->define(Course::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'status' => $faker->randomElement(Course::STATUSES),
        'summary' => $faker->sentence,
        'description' => $faker->text,
        'picture' => $faker->imageUrl(),
        'picture_thumb' => $faker->imageUrl(),
        'age_restrictions' => null,
        'starts_at' => \Carbon\Carbon::now(),
        'ends_at' => null,
        'instructor_id' => $faker->randomNumber()
    ];
});
