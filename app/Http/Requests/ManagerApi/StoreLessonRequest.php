<?php
/**
 * File: StoreLessonRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Lesson\Dto;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Enum\LessonType;
use App\Models\Instructor;
use Illuminate\Validation\Rule;

/**
 * Class StoreLessonRequest
 * @package App\Http\Requests\Api
 */
class StoreLessonRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'classroom_id' => [
                'required',
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
            'instructor_id' => [
                'required_when:type,' . LessonType::LESSON,
                'string',
                'uuid',
                Rule::exists(Instructor::TABLE, 'id')
            ],
            'type' => [
                'required',
                'string',
                Rule::in(LessonType::cases())
            ],
            'starts_at' => [
                'required',
                'date_format:"Y-m-d H:i:s"',
            ],
            'ends_at' => [
                'required',
                'date_format:"Y-m-d H:i:s"',
            ],
        ];
    }

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->classroom_id = $validated['classroom_id'] ?? null;
        $dto->course_id = $validated['course_id'] ?? null;
        $dto->instructor_id = $validated['instructor_id'] ?? null;
        $dto->type = $validated['type'] ?? LessonType::LESSON;
        $dto->starts_at = \Carbon\Carbon::parse($validated['starts_at']);
        $dto->ends_at = \Carbon\Carbon::parse($validated['ends_at']);

        return $dto;
    }
}
