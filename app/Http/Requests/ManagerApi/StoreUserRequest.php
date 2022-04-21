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

/**
 * Class StoreUserRequest
 * @package App\Http\Requests\Api
 */
class StoreUserRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
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

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();

        $dto = new Dto($this->user());
        $dto->username = $validated['username'];
        $dto->password = $validated['password'];

        return $dto;
    }
}
