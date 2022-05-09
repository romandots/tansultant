<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Intent\Dto;
use App\Models\Enum\IntentEventType;
use App\Models\Lesson;
use App\Models\Student;
use Illuminate\Validation\Rule;

class StoreIntentRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Student::TABLE, 'id'),
            ],
            'event_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Lesson::TABLE, 'id'),
            ],
            'event_type' => [
                'required',
                'string',
                Rule::in(enum_strings(IntentEventType::class)),
            ],
        ];
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->manager_id = $this->user()->id;
        $dto->student_id = $validated['student_id'];
        $dto->event_id = $validated['status'];
        $dto->event_type = IntentEventType::from($validated['event_typee']);
        return $dto;
    }
}