<?php

namespace App\Common\Requests;

use App\Common\DTO\ShowDto;

class ShowRequest extends BaseRequest
{
    public function rules(): array
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

    /**
     * @return ShowDto
     */
    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();

        $dto = new ShowDto($this->user());
        $dto->id = $this->getId();
        $dto->with = $validated['with'] ?? [];
        $dto->with_count = $validated['with_count'] ?? [];

        return $dto;
    }

    protected function getId(): string
    {
        return $this->route()->parameter('id');
    }
}