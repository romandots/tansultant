<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class InstructorFactory extends Factory
{
    protected $model = \App\Models\Instructor::class;

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
            'description' => $this->faker->words(10, true),
            'picture' => 'http://some.ur/to/picture.jpg',
            'display' => true,
            'status' => $this->faker->randomElement(\App\Models\Instructor::STATUSES),
            'person_id' => \App\Models\Person::factory(),
            'seen_at' => \Carbon\Carbon::now(),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ];
    }
}
