<?php
/**
 * File: BonusFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Bonus;
use Faker\Generator as Faker;

$factory->define(Bonus::class, static function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'name' => $faker->word,
        'amount' => $faker->randomNumber(),
        'type' => $faker->randomElement(Bonus::TYPES),
        'status' => $faker->randomElement(Bonus::STATUSES),
        'account_id' => $faker->randomNumber(),
        'promocode_id' => $faker->uuid,
        'user_id' => $faker->randomNumber(),
        'expired_at' => \Carbon\Carbon::now()->addMonth()
    ];
});
