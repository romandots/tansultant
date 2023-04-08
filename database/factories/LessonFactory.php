<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Schedule;
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
        $dateTime = Carbon::today()->setTime(random_int(1, 23), 0, 0);
        return [
            'id' => \uuid(),
            'name' => $this->faker->words(1, true),
            'branch_id' => Branch::factory(),
            'course_id' => Course::factory(),
            'schedule_id' => Schedule::factory(),
            'classroom_id' => Classroom::factory(),
            'instructor_id' => Instructor::factory(),
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
