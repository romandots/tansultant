<?php

namespace App\Http\Requests\ManagerApi\DTO;

use App\Common\DTO\SearchFilterDto;

class SearchSubscriptionsFilterDto extends SearchFilterDto
{
    public ?string $student_id = null;
    public ?string $tariff_id = null;
    public array $courses_ids = [];
    public array $statuses = [];
}