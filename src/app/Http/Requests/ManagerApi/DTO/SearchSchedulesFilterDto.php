<?php

namespace App\Http\Requests\ManagerApi\DTO;

use App\Common\DTO\SearchFilterDto;

class SearchSchedulesFilterDto extends SearchFilterDto
{
    public ?\Carbon\Carbon $date;
    public ?string $branch_id;
}