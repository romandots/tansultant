<?php
declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class VisitFactory extends Factory
{
    protected $model = \App\Models\Visit::class;

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
            'student_id' => \uuid(),
            'manager_id' => null,
            'event_type' => \App\Models\Lesson::class,
            'event_id' => \uuid(),
            'payment_type' => \App\Models\Payment::class,
            'payment_id' => \uuid()
        ];
    }
}
