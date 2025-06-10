<?php
/**
 * File: StoreStudentRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Common\Requests;

use App\Common\DTO\IdsDto;

class ManageRelationsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ids' => [
                'nullable',
                'array',
            ],
            'ids.*' => [
                'required_with:ids',
                'string',
                'uuid',
            ],
            'with' => [
                'nullable',
                'array',
            ],
            'with.*' => [
                'string',
            ],
            'with_count' => [
                'nullable',
                'array',
            ],
            'with_count.*' => [
                'string',
            ],
        ];
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new IdsDto($this->user());

        $dto->id = $this->getId();
        $dto->relations_ids = $validated['ids'];
        $dto->with = $validated['with'] ?? [];
        $dto->with_count = $validated['with_count'] ?? [];

        return $dto;
    }

    protected function getId(): ?string
    {
        return $this->route()?->parameter('id');
    }
}
