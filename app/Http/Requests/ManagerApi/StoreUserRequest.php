<?php
/**
 * File: StoreUserRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Person;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreUserRequest
 * @package App\Http\Requests\Api
 */
class StoreUserRequest extends FormRequest
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
            'last_name' => [
                'required',
                'string'
            ],
            'first_name' => [
                'required',
                'string'
            ],
            'patronymic_name' => [
                'required',
                'string'
            ],
            'birth_date' => [
                'required',
                'string',
                'date'
            ],
            'gender' => [
                'required',
                'string',
                Rule::in(Person::GENDER)
            ],
            'phone' => [
                'required',
                'string'
            ],
            'email' => [
                'nullable',
                'string',
                'email'
            ],
            'instagram_username' => [
                'nullable',
                'string'
            ],
            'telegram_username' => [
                'nullable',
                'string'
            ],
            'vk_url' => [
                'nullable',
                'string',
                'url'
            ],
            'facebook_url' => [
                'nullable',
                'string',
                'url'
            ],
            'note' => [
                'nullable',
                'string'
            ],
        ];
    }

    /**
     * @return DTO\StorePerson
     */
    public function getPersonDto(): DTO\StorePerson
    {
        $validated = $this->validated();

        $dto = new DTO\StorePerson;
        $dto->last_name = $validated['last_name'] ?? null;
        $dto->first_name = $validated['first_name'] ?? null;
        $dto->patronymic_name = $validated['patronymic_name'] ?? null;
        $dto->birth_date = $validated['birth_date'] ? \Carbon\Carbon::parse($validated['birth_date']) : null;
        $dto->gender = $validated['gender'] ?? null;
        $dto->phone = $validated['phone'] ? \phone_format($validated['phone']) : null;
        $dto->email = $validated['email'] ?? null;
        $dto->instagram_username = $validated['instagram_username'] ?? null;
        $dto->telegram_username = $validated['telegram_username'] ?? null;
        $dto->vk_url = $validated['vk_url'] ?? null;
        $dto->facebook_url = $validated['facebook_url'] ?? null;
        $dto->note = $validated['note'] ?? null;
        $dto->picture = $this->file('picture');

        return $dto;
    }

    /**
     * @return DTO\User
     */
    public function getUserDto(): DTO\User
    {
        $validated = $this->validated();

        $dto = new DTO\User;
        $dto->username = $validated['username'];
        $dto->password = $validated['password'];

        return $dto;
    }
}
