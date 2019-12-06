<?php
/**
 * File: RegisterUserRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\UserApi;

use App\Http\Requests\DTO\RegisterUser;
use Illuminate\Foundation\Http\FormRequest;

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
        ];
    }

    /**
     * @return RegisterUser
     */
    public function getDto(): RegisterUser
    {
        $validated = $this->validated();

        $dto = new RegisterUser;
        $dto->birth_date = null;
        // @todo implement this

        return $dto;
    }
}
