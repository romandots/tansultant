<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enum\CourseStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class CourseFactory extends Factory
{
    protected $model = \App\Models\Course::class;

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
            'status' => CourseStatus::ACTIVE->value,
            'summary' => $this->faker->words(10, true),
            'description' => $this->faker->words(20, true),
            'display' => true,
            'picture' => 'http://some.ur/to/picture.jpg',
            'picture_thumb' => 'http://some.ur/to/picture.thumb.jpg',
            'age_restrictions' => ['from' => null, 'to' => null],
            'starts_at' => \Carbon\Carbon::now(),
            'ends_at' => \Carbon\Carbon::now()->addYear(),
            'instructor_id' => \App\Models\Instructor::factory(),
        ];
    }
}
