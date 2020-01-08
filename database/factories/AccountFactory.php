<?php
declare(strict_types=1);

/* @var \Illuminate\Database\Eloquent\Factory  $factory */

use App\Models\Account;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    return [
        'id' => \uuid(),
        'name' => $faker->name,
        'type' => $faker->randomElement(Account::TYPES),
        'owner_type' => $faker->randomElement(Account::OWNER_TYPES),
        'owner_id' => \uuid()
    ];
});
