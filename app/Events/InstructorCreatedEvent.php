<?php
declare(strict_types=1);

namespace App\Events;

use App\Models\Instructor;

class InstructorCreatedEvent extends BaseEvent
{
    public Instructor $instructor;

    public function __construct(Instructor $instructor)
    {
        $this->instructor = $instructor;
    }
}
