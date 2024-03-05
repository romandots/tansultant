<?php
/**
 * File: StorePersonRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Subscription\CheckSubscriptionsDto;
use App\Models\Student;
use Illuminate\Validation\Rule;

class CheckSubscriptionsRequest extends StoreRequest
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
            'lessons_ids' => [
                'required',
                'array',
            ],
            'lessons_ids.*' => [
                'string',
                'uuid',
            ],
        ];
    }

    /**
     * @return CheckSubscriptionsDto
     */
    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new CheckSubscriptionsDto($this->user());
        $dto->student_id = $validated['student_id'];
        $dto->lessons_ids = $validated['lessons_ids'] ?? [];

        return $dto;
    }

}
