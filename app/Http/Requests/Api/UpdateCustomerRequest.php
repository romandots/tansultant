<?php
/**
 * File: UpdateCustomerRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateCustomerRequest
 * @package App\Http\Requests\Api
 */
class UpdateCustomerRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'person_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(\App\Models\Person::TABLE)
            ],
        ];
    }

    /**
     * @return DTO\Customer
     */
    public function getDto(): DTO\Customer
    {
        $validated = $this->validated();

        $dto = new DTO\Customer;
        $dto->person_id = $validated['person_id'];

        return $dto;
    }
}
