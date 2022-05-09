<?php
/*
 * File: PaginatedFormRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Common\Requests;

use App\Common\DTO\DtoWithUser;
use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;

abstract class FilteredPaginatedRequest extends BaseRequest
{
    abstract protected function makeSearchDto(): SearchDto;
    abstract protected function makeSearchFilterDto(): SearchFilterDto;
    abstract protected function getSortable(): array;
    abstract protected function mapSearchDto(SearchDto $dto, array $datum): void;
    abstract protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void;
    abstract public function rules(): array;

    /**
     * @return SearchDto
     */
    final public function getDto(): DtoWithUser
    {
        $dto = $this->getSearchDto();
        $dto->filter = $this->getSearchFilterDto();

        return $dto;
    }

    final protected function getSearchDto(): SearchDto
    {
        $validated = $this->validated();
        $dto = $this->makeSearchDto();
        $this->mapSearchDto($dto, $validated);

        return $dto;
    }

    final protected function getSearchFilterDto(): SearchFilterDto
    {
        $validated = $this->validated();
        $dto = $this->makeSearchFilterDto();
        $this->mapSearchFilterDto($dto, $validated);

        return $dto;
    }
}