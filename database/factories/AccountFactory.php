<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Enum\AccountOwnerType;
use App\Models\Enum\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

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
            'type' => $this->faker->randomElement(AccountType::cases()),
            'owner_type' => $this->faker->randomElement(AccountOwnerType::cases()),
            'owner_id' => \uuid()
        ];
    }
}
