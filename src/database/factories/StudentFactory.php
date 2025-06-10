<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enum\StudentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class StudentFactory extends Factory
{
    protected $model = \App\Models\Student::class;

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
            'card_number' => $this->faker->unique()->numerify('####'),
            'status' => $this->faker->randomElement(enum_strings(StudentStatus::class)),
            'person_id' => \uuid(),
            'customer_id' => \uuid(),
            'seen_at' => \Carbon\Carbon::now(),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ];
    }
}
