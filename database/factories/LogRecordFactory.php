<?php
declare(strict_types=1);

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\LogRecord::class, static function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'action' => $faker->word,
        'object_type' => \App\Models\Course::class,
        'object_id' => $faker->uuid,
        'user_id' => \factory(\App\Models\User::class),
        'message' => $faker->sentence,
        'old_value' => null,
        'new_value' => null,
        'created_at' => \Carbon\Carbon::now(),
    ];
});
