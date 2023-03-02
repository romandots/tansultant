<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Enum\PayoutStatus;
use App\Models\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payout>
 */
class PayoutFactory extends Factory
{
    protected $model = \App\Models\Payout::class;

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
            'name' => 'Тестовая зарплата для ' . $this->faker->name,
            'amount' => null,
            'status' => PayoutStatus::CREATED,
            'branch_id' => Branch::factory(),
            'instructor_id' => Instructor::factory(),
            'period_from' => \Carbon\Carbon::now()->subMonth(),
            'period_to' => \Carbon\Carbon::now(),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ];
    }
}
