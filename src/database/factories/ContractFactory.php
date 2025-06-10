<?php
declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ContractFactory extends Factory
{
    protected $model = \App\Models\Contract::class;

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
            'serial' => 'TEST',
            'number' => $this->faker->numerify('######'),
            'branch_id' => \uuid(),
            'customer_id' => \uuid(),
            'status' => 'pending',
            'signed_at' => \Carbon\Carbon::now(),
            'terminated_at' => null,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ];
    }
}
