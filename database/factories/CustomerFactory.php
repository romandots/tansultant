<?php
/**
 * File: CustomerFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Customer::class, static function (Faker $faker) {
    return [
        'name' => $faker->name,
        'person_id' => $faker->numerify('####'),
        'seen_at' => \Carbon\Carbon::now(),
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now()
    ];
});
