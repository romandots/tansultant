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
use App\Components\Student\Dto;
use App\Models\Person;
use App\Models\Student;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'person_id' => [
                'required',
                'string',
                Rule::exists(Person::TABLE, 'id'),
            ],
            'card_number' => [
                'nullable',
                'string',
                Rule::unique(Student::TABLE)->ignore($this->getStudentId()),
            ],
        ];
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->card_number = $validated['card_number'];
        $dto->person_id = $validated['person_id'];

        return $dto;
    }

    protected function getStudentId(): ?string
    {
        return $this->route()?->parameter('id');
    }
}
