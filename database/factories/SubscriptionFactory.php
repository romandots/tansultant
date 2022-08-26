<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enum\SubscriptionStatus;
use App\Models\Student;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class SubscriptionFactory extends Factory
{
    protected $model = \App\Models\Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition()
    {
        return [
            'id' => \uuid(),
            'name' => $this->faker->name,
            'courses_count' => null,
            'visits_count' => 8,
            'days_count' => 28,
            'holds_count' => 1,
            'status' => SubscriptionStatus::ACTIVE,
            'student_id' => Student::factory(),
            'tariff_id' => Tariff::factory(),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ];
    }
}
