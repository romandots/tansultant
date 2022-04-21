<?php
/*
 * File: SearchPeopleRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 12.3.2021
 * Copyright (c) 2021
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Contracts\FilteredInterface;
use App\Common\Requests\FilteredPaginatedRequest;
use App\Http\Requests\ManagerApi\DTO\SearchInstructorsFilterDto;
use App\Models\Enum\InstructorStatus;
use Illuminate\Validation\Rule;

class SearchInstructorsRequest extends FilteredPaginatedRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->paginationDto = new DTO\SearchInstructorsDto();
        $this->filterDto = new DTO\SearchInstructorsFilterDto();
        $this->sortable = ['name', 'status', 'created_at', 'updated_at', 'deleted_at', 'seen_at'];
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'statuses' => [
                    'nullable',
                    'array',
                ],
                'statuses.*' => [
                    'required_with:statuses',
                    'string',
                    Rule::in(InstructorStatus::cases()),
                ],
                'display' => [
                    'nullable',
                    'boolean',
                ],
            ]
        );
    }

    protected function getFilterDto(): FilteredInterface
    {
        $filter = parent::getFilterDto();
        if (!$filter instanceof SearchInstructorsFilterDto) {
            return $filter;
        }

        $validated = $this->validated();
        $filter->statuses = isset($validated['statuses']) ? (array)$validated['statuses'] : null;
        $filter->display = isset($validated['display']) ? (bool)$validated['display'] : null;

        return $filter;
    }
}
