<?php
/**
 * File: VerificationCodeFactory.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-19
 * Copyright (c) 2020
 */
declare(strict_types=1);

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(\App\Models\VerificationCode::class, function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'phone_number' => $faker->e164PhoneNumber,
        'verification_code' => $faker->numerify('####'),
        'created_at' => Carbon::now(),
        'expired_at' => Carbon::now()->addMinute(),
        'verified_at' => null,
    ];
});
