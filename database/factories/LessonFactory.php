<?php
declare(strict_types=1);

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class LessonFactory extends Factory
{
    protected $model = \App\Models\Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition()
    {
        $dateTime = $this->faker->dateTime;
        return [
            'id' => \uuid(),
            'name' => $this->faker->name,
            'branch_id' => \uuid(),
            'course_id' => \uuid(),
            'schedule_id' => \uuid(),
            'classroom_id' => \uuid(),
            'instructor_id' => \uuid(),
            'controller_id' => null,
            'type' => $this->faker->randomElement(enum_strings(\App\Models\Enum\LessonType::class)),
            'status' => $this->faker->randomElement(enum_strings(\App\Models\Enum\LessonStatus::class)),
            'starts_at' => $dateTime,
            'ends_at' => Carbon::parse($dateTime)->addHour(),
            'closed_at' => null,
            'canceled_at' => null,
        ];
    }
}
