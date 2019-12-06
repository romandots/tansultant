<?php
/**
 * File: AttachUserRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

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
                'string',
                'uuid',
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
     * @return DTO\StoreUser
     */
    public function getDto(): DTO\StoreUser
    {
        $validated = $this->validated();

        $dto = new DTO\StoreUser;
        $dto->person_id = $validated['person_id'];
        $dto->username = $validated['username'];
        $dto->password = $validated['password'];

        return $dto;
    }
}
