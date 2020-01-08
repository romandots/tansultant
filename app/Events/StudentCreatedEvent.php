<?php
declare(strict_types=1);

namespace App\Events;

use App\Models\Student;

class StudentCreatedEvent extends BaseEvent
{
    public Student $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }
}
