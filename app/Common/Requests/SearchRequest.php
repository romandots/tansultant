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

    /**
     * @param string $searchDtoClass
     * @param string $searchFilterDtoClass
     * @param array|string[] $sortable
     */
    public function __construct(string $searchDtoClass, string $searchFilterDtoClass, array $sortable)
    {
        parent::__construct();
        $this->searchDtoClass = $searchDtoClass;
        $this->searchFilterDtoClass = $searchFilterDtoClass;
        $this->sortable = $sortable;
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