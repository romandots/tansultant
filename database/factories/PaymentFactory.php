<?php
declare(strict_types=1);

/* @var \Illuminate\Database\Eloquent\Factory  $factory */

use App\Models\Payment;
use Faker\Generator as Faker;

$factory->define(Payment::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'name' => $faker->name,
        'amount' => $faker->randomNumber(),
        'type' => $faker->randomElement(Payment::TYPES),
        'transfer_type' => $faker->randomElement(Payment::TRANSFER_TYPES),
        'status' => $faker->randomElement(Payment::STATUSES),
        'object_type' => $faker->randomElement(Payment::OBJECT_TYPES),
        'object_id' => \uuid(),
        'account_id' => \uuid(),
        'related_id' => null,
        'external_id' => null,
        'user_id' => null,
    ];
});
