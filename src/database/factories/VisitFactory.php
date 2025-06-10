<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enum\VisitEventType;
use App\Models\Enum\VisitPaymentType;
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
            'event_type' => VisitEventType::LESSON,
            'event_id' => \uuid(),
            'payment_type' => VisitPaymentType::PAYMENT,
            'payment_id' => \uuid(),
        ];
    }
}
