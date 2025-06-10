<?php

namespace App\Http\Requests\ManagerApi;

use App\Http\Requests\ManagerApi\DTO\SearchDto;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'query' => [
                'required',
                'string',
            ],
        ];
    }

    public function getDto(): SearchDto
    {
        $dto = new SearchDto($this->user());
        $dto->query = $this->validated('query');

        return $dto;
    }
}