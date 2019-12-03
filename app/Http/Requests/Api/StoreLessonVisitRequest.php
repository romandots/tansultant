<?php
/**
 * File: StoreVisitRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Lesson;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreLessonVisitRequest
 * @package App\Http\Requests\Api
 * @property-read int $student_id
 * @property-read int $lesson_id
 * @property-read int $promocode_id
 */
class StoreLessonVisitRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Student::TABLE, 'id')
            ],
            'lesson_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Lesson::TABLE, 'id')
            ],
            'promocode_id' => [
                'nullable',
                'string',
                'uuid',
//                Rule::exists(\App\Models\Promocode::TABLE, 'id')
            ],
        ];
    }

    /**
     * @return DTO\LessonVisit
     */
    public function getDto(): DTO\LessonVisit
    {
        $validated = $this->validated();

        $dto = new DTO\LessonVisit;
        $dto->student_id = $validated['student_id'];
        $dto->lesson_id = $validated['lesson_id'];
        $dto->promocode_id = $validated['promocode_id'] ?? null;

        return $dto;
    }
}
