<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchPayoutsFilterDto;

class SearchPayoutsRequest extends SearchRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->searchDtoClass = SearchDto::class;
        $this->searchFilterDtoClass = SearchPayoutsFilterDto::class;
        $this->sortable = ['created_at'];
    }

    public function rules(): array
    {
        return \array_merge(parent::rules(), []);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchPayoutsFilterDto);

        parent::mapSearchFilterDto($dto, $datum);
    }
}