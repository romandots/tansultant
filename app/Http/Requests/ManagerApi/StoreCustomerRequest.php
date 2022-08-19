<?php
/**
 * File: StoreCustomerRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Customer\Dto;
use App\Models\Person;
use Illuminate\Validation\Rule;

/**
 * Class StoreCustomerRequest
 * @package App\Http\Requests\Api
 */
class StoreCustomerRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'person_id' =>  [
                'required',
                'string',
                'uuid',
                Rule::exists(Person::TABLE, 'id'),
                //Rule::unique(Customer::TABLE, 'person_id')->ignore($this->getCustomerId()), // performed inside service
            ],
        ];
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();

        $dto = new Dto($this->user());
        $dto->person_id = $validated['person_id'];

        return $dto;
    }

    private function getCustomerId(): ?string
    {
        return $this->route()?->parameter('id');
    }
}
