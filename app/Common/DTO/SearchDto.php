<?php
/*
 * File: FilteredPaginatedDto.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

declare(strict_types=1);

namespace App\Common\DTO;

class SearchDto extends DtoWithUser
{
    public SearchFilterDto $filter;
    public int $offset;
    public int $limit;
    public string $sort;
    public string $order;
    public array $with = [];

    public function getMeta(int $totalRecords): array
    {
        $meta = [
            'sort' => $this->sort,
            'order' => $this->order,
            'offset' => $this->offset,
            'limit' => $this->limit,
            'with' => $this->with,
            'total' => $totalRecords,
        ];

        if (isset($this->filter)) {
            $meta['filter'] = $this->filter->toArray();
        }

        return $meta;
    }

    /**
     * @return SearchFilterDto
     */
    public function getFilter(): SearchFilterDto
    {
        return $this->filter;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getSort(): string
    {
        return $this->sort;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }
}