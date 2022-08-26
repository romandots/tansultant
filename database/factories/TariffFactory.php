<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enum\TariffStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class TariffFactory extends Factory
{
    protected $model = \App\Models\Tariff::class;

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
            'price' => 1000,
            'prolongation_price' => 800,
            'courses_count' => null,
            'visits_count' => 8,
            'days_count' => 28,
            'holds_count' => 1,
            'status' => TariffStatus::ACTIVE,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ];
    }
}
