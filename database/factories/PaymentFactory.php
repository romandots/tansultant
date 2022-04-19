<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enum\PaymentObjectType;
use App\Models\Enum\PaymentStatus;
use App\Models\Enum\PaymentTransferType;
use App\Models\Enum\PaymentType;
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
            'type' => $this->faker->randomElement(PaymentType::cases()),
            'transfer_type' => $this->faker->randomElement(PaymentTransferType::cases()),
            'status' => $this->faker->randomElement(PaymentStatus::cases()),
            'object_type' => $this->faker->randomElement(PaymentObjectType::cases()),
            'object_id' => \uuid(),
            'account_id' => \uuid(),
            'related_id' => null,
            'external_id' => null,
            'user_id' => null,
        ];
    }
}
