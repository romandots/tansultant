<?php
/**
 * File: StoreCourseRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Course\Dto;
use App\Models\Enum\CourseStatus;
use App\Models\Enum\InstructorStatus;
use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

/**
 * Class StoreCourseRequest
 * @package App\Http\Requests\Api
 */
class StoreCourseRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'name' => [
                'required',
                'string'
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(enum_strings(CourseStatus::class))
            ],
            'summary' => [
                'nullable',
                'string'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'display' => [
                'nullable',
                'bool'
            ],
            'age_restrictions_from' => [
                'nullable',
                'int',
                'min:0',
                'max:100',
            ],
            'age_restrictions_to' => [
                'nullable',
                'int',
                'min:0',
                'max:100',
            ],
            'instructor_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Instructor::TABLE, 'id')
                    ->whereNot('status', InstructorStatus::FIRED->value)
            ],
            'starts_at' => [
                'nullable',
                'date'
            ],
            'ends_at' => [
                'nullable',
                'date'
            ],
            'genres' => [
                'nullable',
                'array'
            ],
            'genres.*' => [
                'string',
            ],
        ]);
    }

    /**
     * @return Dto
     */
    public function getDto(): Dto
    {
        $validated = $this->validated();

        $dto = new Dto($this->user());
        $dto->name = $validated['name'];
        $dto->status = isset($validated['status']) ? CourseStatus::from($validated['status']) : CourseStatus::PENDING;
        $dto->summary = $validated['summary'] ?? null;
        $dto->description = $validated['description'] ?? null;
        $dto->display = (bool)($validated['display'] ?? false);
        $dto->age_restrictions = [
            'from' => isset($validated['age_restrictions_from']) ? (int)$validated['age_restrictions_from'] : null,
            'to' => isset($validated['age_restrictions_to']) ? (int)$validated['age_restrictions_to'] : null,
        ];
        $dto->picture = $this->file('picture');
        $dto->instructor_id = $validated['instructor_id'] ?? null;
        $dto->starts_at = isset($validated['starts_at']) ? Carbon::parse($validated['starts_at']) : null;
        $dto->ends_at = isset($validated['ends_at']) ? Carbon::parse($validated['ends_at']) : null;
        $dto->genres = $validated['genres'] ?? [];

        return $dto;
    }
}
