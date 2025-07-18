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
use App\Models\Bonus;
use App\Models\Enum\VisitEventType;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subscription;
use Illuminate\Validation\Rule;

class StoreVisitRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
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
            'bonus_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Bonus::TABLE, 'id')
            ],
            'subscription_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Subscription::TABLE, 'id')
            ],
            'pay_from_balance' => [
                'nullable',
                'bool',
            ],
        ]);
    }

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->student_id = $validated['student_id'];
        $dto->event_id = $validated['lesson_id'];
        $dto->bonus_id = $validated['bonus_id'] ?? null;
        $dto->subscription_id = $validated['subscription_id'] ?? null;
        $dto->event_type = VisitEventType::LESSON;
        $dto->pay_from_balance = (bool)($validated['pay_from_balance'] ?? false);

        return $dto;
    }
}
