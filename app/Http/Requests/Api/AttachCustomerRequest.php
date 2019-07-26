<?php
/**
 * File: AttachCustomerRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Person;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class AttachCustomerRequest
 * @property-read int $person_id
 * @package App\Http\Requests\Api
 */
class AttachCustomerRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'person_id' => [
                'required',
                'integer',
                Rule::exists(Person::TABLE, 'id')
            ]
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
