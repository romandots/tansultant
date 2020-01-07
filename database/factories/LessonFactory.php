<?php
declare(strict_types=1);

/* @var \Illuminate\Database\Eloquent\Factory  $factory */

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(\App\Models\Lesson::class, static function (Faker $faker) {
    $dateTime = $faker->dateTime;
    return [
        'id' => \uuid(),
        'name' => $faker->name,
        'branch_id' => \uuid(),
        'course_id' => \uuid(),
        'schedule_id' => \uuid(),
        'classroom_id' => \uuid(),
        'instructor_id' => \uuid(),
        'controller_id' => null,
        'type' => $faker->randomElement(\App\Models\Lesson::TYPES),
        'status' => $faker->randomElement(\App\Models\Lesson::STATUSES),
        'starts_at' => $dateTime,
        'ends_at' => Carbon::parse($dateTime)->addHour(),
        'closed_at' => null,
        'canceled_at' => null,
    ];
});
