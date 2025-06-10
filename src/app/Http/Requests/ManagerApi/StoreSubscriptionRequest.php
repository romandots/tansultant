<?php
/**
 * File: StoreStudentRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Subscription\Dto;
use App\Models\Student;
use App\Models\Tariff;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends StoreRequest
{
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'tariff_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Tariff::TABLE, 'id'),
            ],
            'student_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Student::TABLE, 'id'),
            ],
        ]);
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->tariff_id = $validated['tariff_id'];
        $dto->student_id = $validated['student_id'];

        return $dto;
    }

    protected function getSubscriptionId(): ?string
    {
        return $this->route()?->parameter('id');
    }
}
