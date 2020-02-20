<?php
/**
 * File: FilterCoursesRequest.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-20
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterCoursesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'statuses' => [
                'nullable',
                'array',
            ],
            'statuses.*' => [
                'required_with:statuses',
                'string',
                Rule::in(Course::STATUSES),
            ],
            'instructors_ids' => [
                'nullable',
                'array',
            ],
            'instructors_ids.*' => [
                'required_with:statuses',
                'string',
                'uuid',
                Rule::exists(Instructor::TABLE, 'id'),
            ],
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'sort' => [
                'nullable',
                'string',
                Rule::in(
                    [
                        'id',
                        'name',
                        'status',
                        'created_at',
                        'starts_at',
                        'ends_at',
                    ]
                ),
            ],
            'order' => [
                'nullable',
                'string',
                Rule::in(['asc', 'desc']),
            ],
        ];
    }

    public function getDto(): DTO\FilterCourses
    {
        $dto = new DTO\FilterCourses();
        $validated = $this->validated();

        $dto->statuses = $validated['statuses'] ?? [];
        $dto->instructors_ids = $validated['instructors_ids'] ?? [];

        $dto->page = (int)($validated['page'] ?? 1);
        $dto->perPage = (int)($validated['per_page'] ?? \config('api.per_page', 50));
        $dto->sort = $validated['sort'] ?? 'created_at';
        $dto->order = $validated['order'] ?? 'desc';

        return $dto;
    }
}