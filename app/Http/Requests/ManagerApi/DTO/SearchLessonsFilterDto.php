<?php
/*
 * File: SearchInstructorsFilterDto.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Http\Requests\ManagerApi\DTO;

use App\Http\Requests\DTO\FilteredDto;

class SearchLessonsFilterDto extends FilteredDto
{
    /** @var string[]|null  */
    public ?array $statuses;
}