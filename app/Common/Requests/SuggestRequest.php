<?php

namespace App\Common\Requests;

use App\Common\DTO\DtoWithUser;

class SuggestRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'query' => [
                'nullable',
                'string',
            ]
        ];
    }

    public function getQuery(): ?string
    {
        return $this->query('query');
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        return new DtoWithUser($this->user());
    }
}