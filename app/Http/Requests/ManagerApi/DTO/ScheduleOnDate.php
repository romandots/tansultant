<?php
/**
 * File: ScheduleOnDate.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

use App\Common\DTO\DtoWithUser;
use App\Models\Enum\Weekday;

/**
 * Class ScheduleOnDate
 * @package App\Http\Requests\ManagerApi\DTO
 */
class ScheduleOnDate extends DtoWithUser
{
    public ?string $branch_id;

    public ?string $classroom_id;

    public ?string $course_id;

    public \Carbon\Carbon $date;

    public Weekday $weekday;
}
