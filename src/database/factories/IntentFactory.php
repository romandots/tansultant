<?php
declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class IntentFactory extends Factory
{
    protected $model = \App\Models\Intent::class;

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
            'manager_id' => \uuid(),
            'event_type' => \App\Models\Enum\IntentEventType::LESSON,
            'event_id' => \uuid(),
            'status' => \App\Models\Enum\IntentStatus::EXPECTING,
        ];
    }
}
