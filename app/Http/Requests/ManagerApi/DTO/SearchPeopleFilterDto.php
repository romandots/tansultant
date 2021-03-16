<?php
/*
 * File: SearchPeopleFilterDto.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Http\Requests\ManagerApi\DTO;

use App\Http\Requests\DTO\FilteredDto;

class SearchPeopleFilterDto extends FilteredDto
{
    public ?\Carbon\Carbon $birth_date_from;
    public ?\Carbon\Carbon $birth_date_to;
    public ?string $gender;
}