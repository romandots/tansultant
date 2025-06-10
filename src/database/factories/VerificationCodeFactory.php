<?php
declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class VerificationCodeFactory extends Factory
{
    protected $model = \App\Models\VerificationCode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'phone_number' => $this->faker->phoneNumber,
            'verification_code' => $this->faker->numerify('####'),
            'created_at' => \Carbon\Carbon::now(),
            'expired_at' => \Carbon\Carbon::now()->addMinute(),
            'verified_at' => null,
        ];
    }
}
