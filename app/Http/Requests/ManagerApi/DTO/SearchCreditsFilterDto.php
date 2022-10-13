<?php
/*
 * File: SearchInstructorsFilterDto.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Http\Requests\ManagerApi\DTO;

use App\Common\DTO\SearchFilterDto;

class SearchCreditsFilterDto extends SearchFilterDto
{
    public ?\Carbon\Carbon $date = null;

    public ?string $customer_id = null;
}