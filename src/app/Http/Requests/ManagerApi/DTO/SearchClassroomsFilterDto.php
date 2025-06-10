<?php

namespace App\Http\Requests\ManagerApi\DTO;

use App\Common\DTO\SearchFilterDto;

class SearchClassroomsFilterDto extends SearchFilterDto
{
    public ?string $branch_id;
}