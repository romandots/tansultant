<?php
/*
 * File: SearchPeopleRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 12.3.2021
 * Copyright (c) 2021
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchPeopleFilterDto;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SearchPeopleRequest extends SearchRequest
{

    public function __construct()
    {
        parent::__construct();
        $this->searchDtoClass = SearchDto::class;
        $this->searchFilterDtoClass = SearchPeopleFilterDto::class;
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
                'date',
            ],
            'birth_date_to' => [
                'nullable',
                'string',
                'date',
            ],
            'gender' => [
                'nullable',
                'string',
                Rule::in(enum_strings(\App\Models\Enum\Gender::class)),
            ],
        ]);
    }

    protected function mapSearchDto(SearchDto $dto, array $datum): void
    {
        assert($dto instanceof $this->searchDtoClass);
        parent::mapSearchDto($dto, $datum);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchPeopleFilterDto);
        $dto->birth_date_from = isset($validated['birth_date_from'])
            ? Carbon::parse($validated['birth_date_from']) : null;
        $dto->birth_date_to = isset($validated['birth_date_to'])
            ? Carbon::parse($validated['birth_date_to']) : null;
        $dto->gender = $validated['gender'] ?? null;

        parent::mapSearchFilterDto($dto, $datum);
    }
}
