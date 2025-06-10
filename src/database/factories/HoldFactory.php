<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class HoldFactory extends Factory
{
    protected $model = \App\Models\Hold::class;

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
            'subscription_id' => Subscription::factory(),
            'created_at' => \Carbon\Carbon::now(),
            'starts_at' => \Carbon\Carbon::now(),
            'ends_at' => \Carbon\Carbon::now()->addDays(7),
        ];
    }
}
