<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;

class SearchAccountRequest extends SearchRequest
{
    public function rules(): array
    {
        return parent::rules();
    }

    protected function mapSearchDto(SearchDto $dto, array $datum): void
    {
        assert($dto instanceof $this->searchDtoClass);
        parent::mapSearchDto($dto, $datum);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof $this->searchFilterDtoClass);
        parent::mapSearchFilterDto($dto, $datum);
    }
}