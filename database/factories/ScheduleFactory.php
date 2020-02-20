<?php
/**
 * File: ScheduleFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Schedule;
use Faker\Generator as Faker;

$factory->define(Schedule::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'branch_id' => \factory(\App\Models\Branch::class),
        'classroom_id' => \factory(\App\Models\Classroom::class),
        'course_id' => \factory(\App\Models\Course::class),
        'starts_at' => $faker->time(),
        'ends_at' => $faker->time(),
        'weekday' => 1,
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
        'deleted_at' => null,
    ];
});
