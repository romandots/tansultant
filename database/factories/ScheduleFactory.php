<?php
/**
 * File: ScheduleFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Schedule;
use Faker\Generator as Faker;

$factory->define(Schedule::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'branch_id' => \uuid(),
        'classroom_id' => \uuid(),
        'course_id' => \uuid(),
        'starts_at' => $faker->date(),
        'ends_at' => $faker->date(),
        'duration' => 60,
        'monday' => $faker->time('H:i'),
        'tuesday' => $faker->time('H:i'),
        'wednesday' => $faker->time('H:i'),
        'thursday' => $faker->time('H:i'),
        'friday' => $faker->time('H:i'),
        'saturday' => $faker->time('H:i'),
        'sunday' => $faker->time('H:i'),
    ];
});
