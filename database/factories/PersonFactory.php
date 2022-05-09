<?php
declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class PersonFactory extends Factory
{
    protected $model = \App\Models\Person::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition()
    {
        $username = $this->faker->word;
        return [
            'id' => \uuid(),
            'last_name' => $this->faker->name,
            'first_name' => $this->faker->name,
            'patronymic_name' => $this->faker->name,
            'birth_date' => \Carbon\Carbon::now(),
            'gender' => $this->faker->randomElement(\App\Models\Enum\Gender::cases()),
            'phone' => \normalize_phone_number($this->faker->unique()->phoneNumber),
            'email' => $this->faker->unique()->safeEmail,
            'picture' => 'http://some.ur/to/picture.jpg',
            'picture_thumb' => 'http://some.ur/to/picture.thumb.jpg',
            'instagram_username' => $username,
            'telegram_username' => $username,
            'vk_uid' => $this->faker->numerify('#######'),
            'facebook_uid' => $this->faker->numerify('#######'),
            'note' => null,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ];
    }
}
