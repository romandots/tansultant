<?php

namespace App\Common\Requests;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use Illuminate\Validation\Rule;

class SearchRequest extends FilteredPaginatedRequest
{
    protected string $searchDtoClass;
    protected string $searchFilterDtoClass;
    protected array $sortable = ['created_at'];

    public function __construct()
    {
        parent::__construct();
        $this->searchDtoClass = SearchDto::class;
        $this->searchFilterDtoClass = SearchFilterDto::class;
        $this->sortable = ['created_at'];
    }

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
            'query' => [
                'nullable',
                'string'
            ],
        ];
    }

    protected function mapSearchDto(SearchDto $dto, array $datum): void
    {
        $dto->offset = (int)($datum['offset'] ?? 0);
        $dto->limit = (int)($datum['limit'] ?? 20);
        $dto->sort = $datum['sort'] ?? 'id';
        $dto->order = $datum['order'] ?? 'asc';
        $dto->with = $datum['with'] ?? [];
        $dto->with_count = $datum['with_count'] ?? [];
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        $dto->with_deleted = (bool)($datum['with_deleted'] ?? false);
        $dto->query = $datum['query'] ?? null;
    }

    final protected function getSortable(): array
    {
        return $this->sortable;
    }

    final public function makeSearchDto(): SearchDto
    {
        if (!isset($this->searchDtoClass)) {
            throw new \LogicException('SearchDto is not set');
        }
        $instance = new ($this->searchDtoClass)();
        if (!($instance instanceof SearchDto)) {
            throw new \LogicException('SearchDto is invalid');
        }

        return $instance;
    }

    final public function makeSearchFilterDto(): SearchFilterDto
    {
        if (!isset($this->searchFilterDtoClass)) {
            throw new \LogicException('FilteredDto is not set');
        }

        $instance = new ($this->searchFilterDtoClass)();
        if (!($instance instanceof SearchFilterDto)) {
            throw new \LogicException('FilteredDto is invalid');
        }

        return $instance;
    }
}