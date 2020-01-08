<?php
/**
 * File: RegisterUserRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\MemberApi;

use App\Http\Requests\DTO\RegisterUser;
use App\Models\Person;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
                Rule::in(User::TYPES),
            ],
            'phone' => [
                'required',
                'string',
                'min:9',
            ],
            'verification_code' => [
                'nullable',
                'string',
            ],
            'last_name' => [
                'nullable',
                'string',
            ],
            'first_name' => [
                'nullable',
                'string',
            ],
            'patronymic_name' => [
                'nullable',
                'string',
            ],
            'birth_date' => [
                'nullable',
                'date',
            ],
            'gender' => [
                'nullable',
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
                'nullable',
                'string',
            ],
        ];
    }


    protected function withValidator(Validator $validator): void
    {
        $data = $validator->getData();

        if (isset($data['verification_code'])) {
            $validator->addRules([
                'last_name' => ['required'],
                'first_name' => ['required'],
                'patronymic_name' => ['required'],
                'birth_date' => ['required'],
                'gender' => ['required'],
                'password' => ['required'],
            ]);
        }
    }

    /**
     * @return RegisterUser
     */
    public function getDto(): RegisterUser
    {
        $validated = $this->validated();

        $dto = new RegisterUser();
        $dto->user_type = $validated['user_type'];
        $dto->phone = $validated['phone'];
        $dto->verification_code = $validated['verification_code'] ?? null;
        $dto->last_name = $validated['last_name'] ?? null;
        $dto->first_name = $validated['first_name'] ?? null;
        $dto->patronymic_name = $validated['patronymic_name'] ?? null;
        $dto->birth_date = isset($validated['birth_date']) ? Carbon::parse($validated['birth_date']) : null;
        $dto->gender = $validated['gender'] ?? null;
        $dto->email = $validated['email'] ?? null;
        $dto->password = isset($validated['password']) ? \Hash::make($validated['password']) : null;

        return $dto;
    }
}
