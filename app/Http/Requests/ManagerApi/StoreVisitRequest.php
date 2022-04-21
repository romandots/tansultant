<?php
/**
 * File: StoreVisitRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Visit\Dto;
use App\Models\Enum\VisitEventType;
use App\Models\Lesson;
use App\Models\Student;
use Illuminate\Validation\Rule;

class StoreVisitRequest extends StoreRequest
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

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->student_id = $validated['student_id'];
        $dto->event_id = $validated['lesson_id'];
        $dto->event_type = VisitEventType::LESSON;
        $dto->promocode_id = $validated['promocode_id'] ?? null;

        return $dto;
    }
}
