<?php
/**
 * File: StoreStudentRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Components\Student\Dto;
use App\Models\Customer;
use App\Models\Person;
use App\Models\Student;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends StoreStudentRequest
{
    public function rules(): array
    {
        return [
            'person_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Person::TABLE, 'id'),
                Rule::unique(Student::TABLE, 'person_id')->ignore($this->getStudentId()),
            ],
            'customer_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Customer::TABLE, 'id'),
            ],
            //'card_number' => [
            //    'nullable',
            //    'string',
            //    Rule::unique(Student::TABLE)->ignore($this->getStudentId()),
            //],
        ];
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        //$dto->card_number = $validated['card_number'];
        $dto->person_id = $validated['person_id'];
        $dto->customer_id = $validated['customer_id'];

        return $dto;
    }

    protected function getStudentId(): ?string
    {
        return $this->route()?->parameter('id');
    }
}
