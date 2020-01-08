<?php
/**
 * File: VisitFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

use App\Models\Visit;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory  $factory */

$factory->define(Visit::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'student_id' => \uuid(),
        'manager_id' => null,
        'event_type' => \App\Models\Lesson::class,
        'event_id' => \uuid(),
        'payment_type' => \App\Models\Payment::class,
        'payment_id' => \uuid()
    ];
});
