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
                Rule::in(Person::TABLE, 'id'),
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
}
