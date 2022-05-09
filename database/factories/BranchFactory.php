<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
                'id' => $this->faker->uuid(),
                'name' => $this->faker->name(),
                'summary' => $this->faker->words(10, true),
                'description' => $this->faker->words(20, true),
//                'address' => [
//                    'country' => 'Россия',
//                    'city' => 'Россия',
//                    'street' => 'Гагарина',
//                    'building' => $this->faker->numerify('###/#'),
//                    'coordinates' => [$this->faker->numerify('45,#####'), $this->faker->numerify('38,#####')],
//                ],
                'phone' => $this->faker->phoneNumber,
                'email' => $this->faker->word . '@' . $this->faker->word . '.com',
                'number' => null
            ];
    }
}
