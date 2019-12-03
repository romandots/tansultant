<?php
declare(strict_types=1);

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Contract::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'serial' => 'TEST',
        'number' => $faker->numerify('######'),
        'branch_id' => \uuid(),
        'customer_id' => \uuid(),
        'status' => 'pending',
        'signed_at' => \Carbon\Carbon::now(),
        'terminated_at' => null,
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now()
    ];
});
