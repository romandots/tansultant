<?php

namespace App\Common\Requests;

use App\Common\DTO\SuggestDto;

class SuggestRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'query' => [
                'nullable',
                'string',
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

    public function getQuery(): ?string
    {
        return $this->query('query');
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $dto = new SuggestDto($this->user());
        $dto->query = $datum['query'] ?? null;
        $dto->with = $datum['with'] ?? [];
        $dto->with_count = $datum['with_count'] ?? [];

        return $dto;
    }
}