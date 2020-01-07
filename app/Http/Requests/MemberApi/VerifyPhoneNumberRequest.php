<?php
/**
 * File: VerifyPhoneNumberRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\MemberApi;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPhoneNumberRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'string',
                'min:9',
            ],
            'verification_code' => [
                'required',
                'string',
            ],
        ];
    }

    public function getDto(): DTO\VerifyPhoneNumber
    {
        $validated = $this->validated();

        $dto = new DTO\VerifyPhoneNumber();
        $dto->phone = $validated['phone'];
        $dto->verification_code = $validated['verification_code'];

        return $dto;
    }
}
