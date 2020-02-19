<?php
/**
 * File: ResetPasswordRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Repository\VerificationCodeRepository;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    private VerificationCodeRepository $verificationCodes;

    /**
     * RegisterUserRequest constructor.
     * @param VerificationCodeRepository $verificationCodes
     */
    public function __construct(VerificationCodeRepository $verificationCodes)
    {
        $this->verificationCodes = $verificationCodes;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
//                Rule::exists(User::TABLE, 'username'),
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
