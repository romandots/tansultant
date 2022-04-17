<?php

namespace Database\Factories;

use App\Models\Bonus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bonus>
 */
class BonusFactory extends Factory
{
    protected $model = Bonus::class;

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
            'name' => 'Bonus',
            'amount' => 5000,
            'type' => Bonus::TYPE_CODE,
            'status' => Bonus::STATUS_ACTIVATED,
            'account_id' => \App\Models\Account::factory(),
            'promocode_id' => null,
            'user_id' => \App\Models\User::factory(),
            'expired_at' => \Carbon\Carbon::now()->addMonth()
        ];
    }
}
