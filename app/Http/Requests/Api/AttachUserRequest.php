<?php
/**
 * File: AttachUserRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Person;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class AttachUserRequest
 * @property-read int $person_id
 * @package App\Http\Requests\Api
 */
class AttachUserRequest extends FormRequest
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
            ],
            'username' => [
                'required',
                'string'
            ],
            'password' => [
                'required',
                'string'
            ],
        ];
    }

    /**
     * @return DTO\User
     */
    public function getDto(): DTO\User
    {
        $validated = $this->validated();

        $dto = new DTO\User;
        $dto->person_id = $validated['person_id'];
        $dto->username = $validated['username'];
        $dto->password = $validated['password'];

        return $dto;
    }
}
