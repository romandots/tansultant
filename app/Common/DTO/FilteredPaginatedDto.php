<?php
/*
 * File: FilteredPaginatedDto.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

declare(strict_types=1);

namespace App\Common\DTO;

use App\Common\Contracts\FilteredInterface;
use App\Common\Contracts\PaginatedInterface;

abstract class FilteredPaginatedDto implements PaginatedInterface
{
    public FilteredInterface $filter;
    public int $offset;
    public int $limit;
    public string $sort;
    public string $order;

    public function getMeta(int $totalRecords): array {
        $meta = [
            'sort' => $this->sort,
            'order' => $this->order,
            'offset' => $this->offset,
            'limit' => $this->limit,
            'total' => $totalRecords,
        ];

        if ($this->filter) {
            $meta['filter'] = $this->filter->toArray();
        }

        return $meta;
    }
}