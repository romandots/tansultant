<?php
/*
 * File: SearchInstructorsFilterDto.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Http\Requests\ManagerApi\DTO;

use App\Common\DTO\SearchFilterDto;

class SearchPaymentsFilterDto extends SearchFilterDto
{
    public ?\Carbon\Carbon $date = null;
}