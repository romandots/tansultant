<?php
/**
 * File: StorePersonRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Person;
use Illuminate\Validation\Rule;

/**
 * Class UpdatePersonRequest
 * @package App\Http\Requests
 */
class UpdatePersonRequest extends StorePersonRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
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
                Rule::in(enum_strings(\App\Models\Enum\Gender::class)),
            ],
            'phone' => [
                'nullable',
                'string',
                //Rule::unique(Person::TABLE, 'phone')->ignore($this->getId()),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                Rule::unique(Person::TABLE, 'email')->ignore($this->getId()),
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
            'picture' => [
                'nullable',
                'file',
                'max:' . \config('uploads.max', 10240),
                'mimes:jpeg,png,pdf'
            ]
        ];
    }
}
