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
use App\Models\Customer;
use App\Models\Person;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends StoreRequest
{
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'person_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Person::TABLE, 'id'),
                //Rule::unique(Student::TABLE, 'person_id')->ignore($this->getStudentId()), // Handled by service
            ],
            'customer_id' => [
                'nullable',
                'required_if:student_is_customer,false',
                'required_without:student_is_customer',
                'string',
                'uuid',
                Rule::exists(Customer::TABLE, 'id'),
            ],
            'student_is_customer' => [
                'nullable',
                'bool',
            ],
            //'card_number' => [
            //    'nullable',
            //    'string',
            //    Rule::unique(Student::TABLE)->ignore($this->getStudentId()),
            //],
        ]);
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        //$dto->card_number = $validated['card_number'];
        $dto->person_id = $validated['person_id'];
        $dto->student_is_customer = (bool)($validated['student_is_customer'] ?? false);
        $dto->customer_id = $dto->student_is_customer ? null : $validated['customer_id'];

        return $dto;
    }

    protected function getStudentId(): ?string
    {
        return $this->route()?->parameter('id');
    }
}
