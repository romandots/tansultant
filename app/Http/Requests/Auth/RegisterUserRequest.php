<?php
/**
 * File: $fileName
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-19
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\DTO\RegisterUser;
use App\Models\Person;
use App\Models\User;
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
    private VerificationCodeRepository $verificationCodes;

    /**
     * RegisterUserRequest constructor.
     * @param VerificationCodeRepository $verificationCodes
     */
    public function __construct(VerificationCodeRepository $verificationCodes)
    {
        $this->verificationCodes = $verificationCodes;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_type' => [
                'required',
                'string',
                Rule::in(\array_map(fn(string $type) => \base_classname($type), User::TYPES)),
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
                Rule::in(Person::GENDER),
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
        $dto->phone = $this->verificationCodes->findVerifiedById($validated['verification_code_id'])->phone_number;
        $dto->last_name = $validated['last_name'];
        $dto->first_name = $validated['first_name'];
        $dto->patronymic_name = $validated['patronymic_name'];
        $dto->birth_date = isset($validated['birth_date']) ? Carbon::parse($validated['birth_date']) : null;
        $dto->gender = $validated['gender'];
        $dto->email = $validated['email'] ?? null;
        $dto->description = $validated['description'] ?? null;
        $dto->password = $validated['password'];

        return $dto;
    }
}
