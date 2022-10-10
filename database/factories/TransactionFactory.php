<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionTransferType;
use App\Models\Enum\TransactionType;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

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
            'type' => $this->faker->randomElement(TransactionType::cases()),
            'transfer_type' => $this->faker->randomElement(TransactionTransferType::cases()),
            'status' => $this->faker->randomElement(TransactionStatus::cases()),
            'branch_id' => Branch::factory(),
            'account_id' => \uuid(),
            'related_id' => null,
            'external_id' => null,
            'user_id' => null,
        ];
    }
}
