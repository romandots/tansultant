<?php
declare(strict_types=1);
/**
 * File: BonusFactory.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

/* @var \Illuminate\Database\Eloquent\Factory  $factory */

use App\Models\Bonus;
use Faker\Generator as Faker;

$factory->define(Bonus::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'name' => 'Bonus',
        'amount' => 5000,
        'type' => Bonus::TYPE_CODE,
        'status' => Bonus::STATUS_ACTIVATED,
        'account_id' => \uuid(),
        'promocode_id' => null,
        'user_id' => \uuid(),
        'expired_at' => \Carbon\Carbon::now()->addMonth()
    ];
});
