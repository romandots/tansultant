<?php
declare(strict_types=1);

namespace App\Events\Student;

use App\Events\BaseModelEvent;
use App\Models\Enum\LogRecordObjectType;
use App\Models\Student;
use App\Models\User;

abstract class StudentEvent extends BaseModelEvent
{
    public function __construct(
        public Student $student,
        public ?User $user = null,
    ) {
    }

    public function getType(): LogRecordObjectType
    {
        return LogRecordObjectType::STUDENT;
    }

    public function getRecordId(): string
    {
        return $this->getStudent()->id;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student
    {
        return $this->student;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public static function created(Student $student, ?User $user = null): void
    {
        StudentCreatedEvent::dispatch($student, $user);
    }
}
