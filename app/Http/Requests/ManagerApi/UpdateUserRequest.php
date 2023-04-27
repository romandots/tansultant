<?php
/**
 * File: StoreUserRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\User\Dto;
use App\Models\Person;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge($this->showRules(), [
            'username' => [
                'nullable',
                'string'
            ],
            'person_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Person::TABLE, 'id'),
            ],
            'roles' => [
                'nullable',
                'array',
            ],
            'roles.*' => [
                'required_with:roles',
                'string',
                Rule::exists('roles', 'name'),
            ],
        ]);
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();

        $dto = new Dto($this->user());
        $dto->username = $validated['username'];
        $dto->person_id = $validated['person_id'];
        $dto->roles = $validated['roles'] ?? [];

        return $dto;
    }
}
