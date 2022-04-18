<?php
/*
 * File: PaginatedFormRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Common\Requests;

use App\Common\Contracts\FilteredInterface;
use App\Common\Contracts\PaginatedInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilteredPaginatedRequest extends FormRequest
{
    protected PaginatedInterface $paginationDto;
    protected FilteredInterface $filterDto;
    protected array $sortable = [];

    public function rules(): array
    {
        return [
            'offset' => [
                'nullable',
                'int'
            ],
            'limit' => [
                'nullable',
                'int'
            ],
            'sort' => [
                'nullable',
                'string',
                Rule::in($this->getSortable())
            ],
            'order' => [
                'nullable',
                'string',
                Rule::in(['desc', 'asc'])
            ],
            'with_deleted' => [
                'nullable',
                'boolean'
            ],
            'query' => [
                'nullable',
                'string'
            ],
        ];
    }

    public function getDto(): PaginatedInterface
    {
        $dto = $this->getPaginationDto();
        $dto->filter = $this->getFilterDto();

        return $dto;
    }

    protected function getPaginationDto(): PaginatedInterface
    {
        $validated = $this->validated();
        $dto = clone $this->paginationDto;
        $dto->offset = (int)($validated['offset'] ?? 0);
        $dto->limit = (int)($validated['limit'] ?? 20);
        $dto->sort = $validated['sort'] ?? 'id';
        $dto->order = $validated['order'] ?? 'asc';

        return $dto;
    }

    protected function getFilterDto(): FilteredInterface
    {
        $validated = $this->validated();
        $dto = clone $this->filterDto;
        $dto->with_deleted = (bool)($validated['with_deleted'] ?? false);
        $dto->query = $validated['query'] ?? null;

        return $dto;
    }

    public function getSortable(): array
    {
        return (array)$this->sortable;
    }
}