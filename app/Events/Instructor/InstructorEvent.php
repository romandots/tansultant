<?php
declare(strict_types=1);

namespace App\Events\Instructor;

use App\Events\BaseModelEvent;
use App\Models\Enum\LogRecordObjectType;
use App\Models\Instructor;
use App\Models\User;

abstract class InstructorEvent extends BaseModelEvent
{
    public function __construct(
        public Instructor $instructor,
        public ?User $user = null,
    ) {
    }

    public function getType(): LogRecordObjectType
    {
        return LogRecordObjectType::STUDENT;
    }

    public function getRecordId(): string
    {
        return $this->getInstructor()->id;
    }

    /**
     * @return Instructor
     */
    public function getInstructor(): Instructor
    {
        return $this->instructor;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public static function created(Instructor $instructor, ?User $user = null): void
    {
        InstructorCreatedEvent::dispatch($instructor, $user);
    }
}
