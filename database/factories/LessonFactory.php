<?php
declare(strict_types=1);

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(\App\Models\Lesson::class, static function (Faker $faker) {
    $dateTime = $faker->dateTime;
    return [
        'name' => $faker->name,
        'branch_id' => $faker->randomNumber(),
        'course_id' => $faker->randomNumber(),
        'schedule_id' => null,
        'classroom_id' => $faker->randomNumber(),
        'instructor_id' => $faker->randomNumber(),
        'controller_id' => null,
        'type' => $faker->randomElement(\App\Models\Lesson::TYPES),
        'status' => $faker->randomElement(\App\Models\Lesson::STATUSES),
        'starts_at' => $dateTime,
        'ends_at' => Carbon::parse($dateTime)->addHour(),
        'closed_at' => null,
        'canceled_at' => null,
    ];
});
