<?php
/**
 * File: ResetPasswordRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                Rule::exists(\App\Models\User::TABLE, 'username'),
            ],
            'verification_code' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function getDto(): DTO\ResetPassword
    {
        $valid = $this->validated();

        $dto = new DTO\ResetPassword();
        $dto->username = $valid['username'];
        $dto->verification_code = $valid['verification_code'] ?? null;

        return $dto;
    }
}
