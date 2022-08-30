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
            'courses_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'visits_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'days_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'holds_count' => [
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
        $dto->courses_count = isset($validated['courses_count']) ? (int)$validated['courses_count'] : null;
        $dto->visits_count = isset($validated['visits_count']) ? (int)$validated['visits_count'] : null;
        $dto->days_count = isset($validated['days_count']) ? (int)$validated['days_count'] : null;
        $dto->holds_count = isset($validated['holds_count']) ? (int)$validated['holds_count'] : null;

        return $dto;
    }

    protected function getTariffId(): ?string
    {
        return $this->route()?->parameter('id');
    }
}
