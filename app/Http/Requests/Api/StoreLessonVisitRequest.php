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
use App\Models\Visit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreLessonVisitRequest
 * @package App\Http\Requests\Api
 */
class StoreLessonVisitRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $paymentType = $this->get('payment_type');

        return [
            'student_id' => [
                'required',
                'integer',
                Rule::exists(Student::TABLE, 'id')
            ],
            'lesson_id' => [
                'required',
                'integer',
                Rule::exists(Lesson::TABLE, 'id')
            ],
            'payment_type' => [
                'required',
                'string',
                Rule::in(Visit::PAYMENT_TYPES)
            ],
            'payment_id' => [
                'nullable',
                'integer',
                Rule::exists($paymentType::TABLE, 'id')
            ],
        ];
    }

    // @todo conditional required for Promocode
//    public function withValidator(Validator $validator): void
//    {
//        $validator->sometimes('payment_id', ['required'], function () {
//            $this->payment_type === Promocode::class;
//        });
//    }

    /**
     * @return DTO\Visit
     */
    public function getDto(): DTO\Visit
    {
        $validated = $this->validated();

        $dto = new DTO\Visit;
        $dto->student_id = $validated['student_id'];
        $dto->event_id = $validated['lesson_id'];
        $dto->event_type = \App\Models\Lesson::class;
        $dto->payment_type = $validated['payment_type'];
        $dto->payment_id = $validated['payment_id'] ?? null;

        return $dto;
    }
}
