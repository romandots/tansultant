<?php
/**
 * File: StoreCourseRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Course;
use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreCourseRequest
 * @package App\Http\Requests\Api
 */
class StoreCourseRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string'
            ],
            'status' => [
                'required',
                'string',
                Rule::in(Course::STATUSES)
            ],
            'summary' => [
                'nullable',
                'string'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'picture' => [
                'nullable',
                'file',
                'max:' . \config('uploads.max', 10240),
                'mimes:jpeg,png,pdf'
            ],
            'age_restrictions' => [
                'nullable',
                'string'
            ],
            'instructor_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Instructor::TABLE, 'id')
            ],
            'starts_at' => [
                'nullable',
                'date'
            ],
            'ends_at' => [
                'nullable',
                'date'
            ],
        ];
    }

    /**
     * @return DTO\StoreCourse
     */
    public function getDto(): DTO\StoreCourse
    {
        $validated = $this->validated();

        $dto = new DTO\StoreCourse;
        $dto->name = $validated['name'];
        $dto->status = $validated['status'];
        $dto->summary = $validated['summary'] ?? null;
        $dto->description = $validated['description'] ?? null;
        $dto->picture = $this->file('picture');
        $dto->age_restrictions = $validated['age_restrictions'] ?? null;
        $dto->instructor_id = $validated['instructor_id'] ?? null;
        $dto->starts_at = isset($validated['starts_at']) ? Carbon::parse($validated['starts_at']) : null;
        $dto->ends_at = isset($validated['ends_at']) ? Carbon::parse($validated['ends_at']) : null;

        return $dto;
    }
}
