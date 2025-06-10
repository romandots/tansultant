<?php
/**
 * File: UpdateUserPasswordRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-21
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\BaseRequest;

/**
 * Class UpdateUserPasswordRequest
 * @package App\Http\Requests\Api
 * @property-read string $old_password
 * @property-read string $new_password
 */
class UpdateUserPasswordRequest extends BaseRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'old_password' => [
                'required',
                'string'
            ],
            'new_password' => [
                'required',
                'string',
                'min:6'
            ]
        ];
    }

    /**
     * @return \App\Components\User\UpdateUserPasswordDto
     */
    public function getDto(): \App\Components\User\UpdateUserPasswordDto
    {
        $validated = $this->validated();

        $dto = new \App\Components\User\UpdateUserPasswordDto;
        $dto->old_password = $validated['old_password'];
        $dto->new_password = $validated['new_password'];

        return $dto;
    }
}
