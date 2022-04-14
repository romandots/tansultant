<?php

namespace Database\Factories;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

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
            'name' => $this->faker->randomLetter,
            'branch_id' => \App\Models\Branch::factory(),
            'color' => $this->faker->colorName,
            'capacity' => $this->faker->numberBetween(10, 25),
            'number' => $this->faker->randomNumber(),
        ];
    }
}
