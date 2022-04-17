<?php
declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ScheduleFactory extends Factory
{
    protected $model = \App\Models\Schedule::class;

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
            'cycle' => \App\Models\Enum\ScheduleCycle::EVERY_WEEK,
            'branch_id' => \App\Models\Branch::factory(),
            'classroom_id' => \App\Models\Classroom::factory(),
            'course_id' => \App\Models\Course::factory(),
            'starts_at' => $this->faker->time(),
            'ends_at' => $this->faker->time(),
            'weekday' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            'deleted_at' => null,
        ];
    }
}
