<?php

declare(strict_types=1);

namespace App\Components\Lesson;

use App\Models\Enum\LessonStatus;
use App\Models\Enum\LessonType;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public string $branch_id;
    public ?string $course_id;
    public ?string $schedule_id = null;
    public string $classroom_id;
    public ?string $instructor_id;
    public ?string $controller_id;
    public ?string $payment_id;
    public LessonType $type;
    public LessonStatus $status;
    public \Carbon\Carbon $starts_at;
    public \Carbon\Carbon $ends_at;
}