<?php
/*
 * File: SearchPeopleRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 12.3.2021
 * Copyright (c) 2021
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchPeopleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'query' => [
                'nullable',
                'string'
            ],
            'offset' => [
                'nullable',
                'int'
            ],
            'limit' => [
                'nullable',
                'int'
            ],
            'sort' => [
                'nullable',
                'string'
            ],
            'order' => [
                'nullable',
                'string',
                Rule::in(['desc', 'asc'])
            ],
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
                Rule::in(Person::GENDER)
            ],
        ];
    }

    public function getDto(): \App\Http\Requests\DTO\SearchPeople
    {
        $validated = $this->validated();
        $dto = new \App\Http\Requests\DTO\SearchPeople();
        $dto->offset = (int)($validated['offset'] ?? 0);
        $dto->limit = (int)($validated['limit'] ?? 20);
        $dto->sort = $validated['sort'] ?? 'id';
        $dto->order = $validated['order'] ?? 'asc';
        $dto->filter = new \App\Http\Requests\DTO\SearchPeopleFilter();
        $dto->filter->query = $validated['query'] ?? null;
        $dto->filter->birth_date_from = isset($validated['birth_date_from'])
            ? Carbon::parse($validated['birth_date_from']) : null;
        $dto->filter->birth_date_to = isset($validated['birth_date_to'])
            ? Carbon::parse($validated['birth_date_to']) : null;
        $dto->filter->gender = $validated['gender'] ?? null;

        return $dto;
    }
}
