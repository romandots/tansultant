<?php
declare(strict_types=1);

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Contract::class, static function (Faker $faker) {
    return [
        'serial' => 'TEST',
        'number' => $faker->numerify('######'),
        'branch_id' => 1,
        'customer_id' => 1,
        'status' => 'pending',
        'signed_at' => \Carbon\Carbon::now(),
        'terminated_at' => null,
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now()
    ];
});
