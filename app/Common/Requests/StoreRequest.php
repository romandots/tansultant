<?php

namespace App\Common\Requests;

use App\Common\DTO\DtoWithUser;
use App\Common\DTO\ShowDto;

abstract class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return $this->showRules();
    }

    public function showRules(): array
    {
        return [
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
        return new DtoWithUser($this->user());
    }

    public function getShowDto(): ShowDto
    {
        $validated = $this->validated();

        $dto = new ShowDto();
        $dto->user = $this->getUser();
        $dto->with = $validated['with'] ?? [];
        $dto->with_count = $validated['with_count'] ?? [];

        return $dto;
    }
}