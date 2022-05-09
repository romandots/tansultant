<?php

declare(strict_types=1);

namespace App\Components\Schedule;

use App\Models\Enum\ScheduleCycle;
use App\Models\Enum\Weekday;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;

    public ?string $branch_id;

    public ?string $classroom_id;

    public ?string $course_id;

    public ScheduleCycle $cycle;

    public ?Weekday $weekday;

    public ?\Carbon\Carbon $from_date;

    public ?\Carbon\Carbon $to_date;

    public ?\Carbon\Carbon $starts_at;

    public ?\Carbon\Carbon $ends_at;
}