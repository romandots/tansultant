<?php
/**
 * File: StoreStudentRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Tariff\Dto;
use App\Models\Enum\TariffStatus;
use App\Models\Tariff;
use Illuminate\Validation\Rule;

class StoreTariffRequest extends StoreRequest
{
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'name' => [
                'required',
                'string',
                Rule::unique(Tariff::TABLE, 'name')->ignore($this->getTariffId()),
            ],
            'price' => [
                'required',
                'integer',
                'min:0',
            ],
            'prolongation_price' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'courses_limit' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'visits_limit' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'days_limit' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'holds_limit' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->status = TariffStatus::ACTIVE;
        $dto->name = $validated['name'];
        $dto->price = (float)$validated['price'];
        $dto->prolongation_price = (float)($validated['prolongation_price'] ?? $validated['price']);
        $dto->courses_limit = isset($validated['courses_limit']) ? (int)$validated['courses_limit'] : null;
        $dto->visits_limit = isset($validated['visits_limit']) ? (int)$validated['visits_limit'] : null;
        $dto->days_limit = isset($validated['days_limit']) ? (int)$validated['days_limit'] : null;
        $dto->holds_limit = isset($validated['holds_limit']) ? (int)$validated['holds_limit'] : null;

        return $dto;
    }

    protected function getTariffId(): ?string
    {
        return $this->route()?->parameter('id');
    }
}
