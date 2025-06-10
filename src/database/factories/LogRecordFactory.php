<?php
declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class LogRecordFactory extends Factory
{
    protected $model = \App\Models\LogRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'action' => $this->faker->word,
            'object_type' => \App\Models\Course::class,
            'object_id' => $this->faker->uuid,
            'user_id' => \App\Models\User::factory(),
            'message' => $this->faker->words(10, true),
            'old_value' => null,
            'new_value' => null,
            'created_at' => \Carbon\Carbon::now(),
        ];
    }
}
