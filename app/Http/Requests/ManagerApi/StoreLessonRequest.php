<?php
/**
 * File: StoreLessonRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Lesson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreLessonRequest
 * @package App\Http\Requests\Api
 */
class StoreLessonRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'classroom_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Classroom::TABLE, 'id')
            ],
            'course_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Course::TABLE, 'id')
            ],
            'branch_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Branch::TABLE, 'id')
            ],
            'instructor_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Instructor::TABLE, 'id')
            ],
            'type' => [
                'nullable',
                'string',
                Rule::in(Lesson::TYPES)
            ],
            'starts_at' => [
                'required',
                'date_format:"Y-m-d H:i"',
            ],
            'ends_at' => [
                'required',
                'date_format:"Y-m-d H:i"',
            ],
        ];
    }

    /**
     * @return DTO\StoreLesson
     */
    public function getDto(): DTO\StoreLesson
    {
        $validated = $this->validated();

        $dto = new DTO\StoreLesson();
        $dto->classroom_id = $validated['classroom_id'] ?? null;
        $dto->course_id = $validated['course_id'] ?? null;
        $dto->branch_id = $validated['branch_id'] ?? null;
        $dto->instructor_id = $validated['instructor_id'] ?? null;
        $dto->type = $validated['type'] ?? Lesson::TYPE_LESSON;
        $dto->starts_at = \Carbon\Carbon::parse($validated['starts_at']);
        $dto->ends_at = \Carbon\Carbon::parse($validated['ends_at']);

        return $dto;
    }
}
