<?php
/**
 * File: StorePersonRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Person\Dto;
use App\Models\Enum\Gender;
use App\Models\Person;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class StorePersonRequest
 * @package App\Http\Requests
 */
class StorePersonRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
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
                //Rule::unique(Person::TABLE, 'phone'), // manual validation used instead
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                Rule::unique(Person::TABLE, 'email'),
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
        ]);
    }

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->last_name = $this->normalizeCase($validated['last_name'] ?? null);
        $dto->first_name = $this->normalizeCase($validated['first_name'] ?? null);
        $dto->patronymic_name = $this->normalizeCase($validated['patronymic_name'] ?? null);
        $dto->birth_date = $validated['birth_date'] ? \Carbon\Carbon::parse($validated['birth_date']) : null;
        $dto->gender = isset($validated['gender']) ? Gender::from($validated['gender']) : null;
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

    protected function getId(): ?string
    {
        return $this->route()->parameter('id');
    }

    private function normalizeCase(?string $string = null): ?string
    {
        return $string ? Str::ucfirst(Str::lower($string)) : null;
    }
}
