<?php
/**
 * File: $fileName
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-19
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\Auth\DTO\RegisterUser;
use App\Models\Enum\UserType;
use App\Models\VerificationCode;
use App\Repository\VerificationCodeRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class RegisterUserRequest
 * @package App\Http\Requests\StudentApi
 */
class RegisterUserRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_type' => [
                'required',
                'string',
                Rule::in(UserType::cases()),
            ],
            'verification_code_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(VerificationCode::TABLE, 'id'),
            ],
            'last_name' => [
                'required',
                'string',
            ],
            'first_name' => [
                'required',
                'string',
            ],
            'patronymic_name' => [
                'required',
                'string',
            ],
            'birth_date' => [
                'required',
                'date',
            ],
            'gender' => [
                'required',
                'string',
                Rule::in(\App\Models\Enum\Gender::cases()),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * @return RegisterUser
     */
    public function getDto(): RegisterUser
    {
        $validated = $this->validated();

        $dto = new RegisterUser();
        $dto->user_type = $validated['user_type'];
        $dto->last_name = $validated['last_name'];
        $dto->first_name = $validated['first_name'];
        $dto->verification_code = $validated['verification_code_id'] ?? null;
        $dto->patronymic_name = $validated['patronymic_name'];
        $dto->birth_date = isset($validated['birth_date']) ? Carbon::parse($validated['birth_date']) : null;
        $dto->gender = $validated['gender'];
        $dto->email = $validated['email'] ?? null;
        $dto->description = $validated['description'] ?? null;
        $dto->password = $validated['password'];

        return $dto;
    }
}
