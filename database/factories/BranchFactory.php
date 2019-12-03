<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Branch;
use Faker\Generator as Faker;

$factory->define(Branch::class, static function (Faker $faker) {
    return [
        'id' => \uuid(),
        'name' => $faker->name,
        'summary' => $faker->sentence,
        'description' => $faker->text,
        'address' => [
            'country' => 'Россия',
            'city' => 'Россия',
            'street' => $faker->streetName,
            'building' => $faker->numerify('###/#'),
            'coordinates' => [$faker->numerify('45,#####'), $faker->numerify('38,#####')],
        ],
        'phone' => $faker->phoneNumber,
        'email' => $faker->email,
        'url' => $faker->url,
        'vk_url' => $faker->url,
        'facebook_url' => $faker->url,
        'telegram_username' => $faker->word,
        'instagram_username' => $faker->word,
        'number' => null
    ];
});
