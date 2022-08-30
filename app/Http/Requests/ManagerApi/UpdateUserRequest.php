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

class UpdateUserRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge($this->showRules(), [
            'name' => [
                'nullable',
                'string'
            ],
            'username' => [
                'required',
                'string'
            ],
            'password' => [
                'required',
                'string'
            ],
        ]);
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();

        $dto = new Dto($this->user());
        $dto->username = $validated['username'];
        $dto->password = $validated['password'];
        $dto->name = $validated['name'] ?? null;

        return $dto;
    }
}
