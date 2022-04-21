<?php
/**
 * File: StoreLessonIntentRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Models\Lesson;
use App\Models\Student;
use Illuminate\Validation\Rule;

/**
 * Class StoreLessonIntentRequest
 * @package App\Http\Requests\Api
 */
class StoreLessonIntentRequest extends StoreRequest
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
            ]
        ];
    }

    /**
     * @return DTO\StoreIntent
     */
    public function getDto(): DTO\StoreIntent
    {
        $validated = $this->validated();

        $dto = new DTO\StoreIntent;
        $dto->student_id = $validated['student_id'];
        $dto->event_id = $validated['lesson_id'];
        $dto->event_type = \App\Models\Lesson::class;

        return $dto;
    }
}
