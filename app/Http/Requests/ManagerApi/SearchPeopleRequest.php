<?php
/*
 * File: SearchPeopleRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 12.3.2021
 * Copyright (c) 2021
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Contracts\SearchFilterDtoContract;
use App\Common\Requests\SearchRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SearchPeopleRequest extends SearchRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->paginationDto = new \App\Http\Requests\ManagerApi\DTO\SearchPeopleDto();
        $this->filterDto = new \App\Http\Requests\ManagerApi\DTO\SearchPeopleFilterDto();
        $this->sortable = [
            'last_name',
            'id',
            'birth_date',
            'created_at',
            'updated_at',
        ];
    }

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'birth_date_from' => [
                'nullable',
                'string',
                'date'
            ],
            'birth_date_to' => [
                'nullable',
                'string',
                'date'
            ],
            'gender' => [
                'nullable',
                'string',
                Rule::in(\App\Models\Enum\Gender::cases())
            ],
        ]);
    }

    protected function getSearchFilterDto(): SearchFilterDtoContract
    {
        $filter = parent::getSearchFilterDto();
        $validated = $this->validated();

        $filter->birth_date_from = isset($validated['birth_date_from'])
            ? Carbon::parse($validated['birth_date_from']) : null;
        $filter->birth_date_to = isset($validated['birth_date_to'])
            ? Carbon::parse($validated['birth_date_to']) : null;
        $filter->gender = $validated['gender'] ?? null;

        return $filter;
    }
}
