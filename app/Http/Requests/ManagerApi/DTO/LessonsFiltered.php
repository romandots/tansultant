<?php
/**
 * File: LessonsOnDate.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

use App\Common\DTO\DtoWIthUser;

class LessonsFiltered extends DtoWIthUser
{
    public \Carbon\Carbon $date;

    public ?string $branch_id;

    public ?string $classroom_id;

    public ?string $course_id;
}
