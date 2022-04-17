<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

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
            'amount' => $this->faker->randomNumber(),
            'type' => $this->faker->randomElement(Payment::TYPES),
            'transfer_type' => $this->faker->randomElement(Payment::TRANSFER_TYPES),
            'status' => $this->faker->randomElement(Payment::STATUSES),
            'object_type' => $this->faker->randomElement(Payment::OBJECT_TYPES),
            'object_id' => \uuid(),
            'account_id' => \uuid(),
            'related_id' => null,
            'external_id' => null,
            'user_id' => null,
        ];
    }
}
