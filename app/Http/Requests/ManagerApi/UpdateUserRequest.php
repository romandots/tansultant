<?php
/**
 * File: UpdateUserRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateUserRequest
 * @package App\Http\Requests\Api
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => [
                'nullable',
                'string',
                'min:6'
            ],
            'username' => [
                'nullable',
                'string',
                'min:3',
                Rule::unique(\App\Models\User::TABLE)->ignore($this->route('user'))
            ],
            'name' => [
                'nullable',
                'string',
                'min:2',
            ],
        ];
    }

    /**
     * @return DTO\UpdateUser
     */
    public function getDto(): DTO\UpdateUser
    {
        $validated = $this->validated();

        $dto = new DTO\UpdateUser;
        $dto->password = $validated['password'] ?? null;
        $dto->name = $validated['name'] ?? null;
        $dto->username = $validated['username'] ?? null;

        return $dto;
    }
}
