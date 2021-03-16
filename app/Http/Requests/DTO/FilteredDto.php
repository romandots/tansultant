<?php
/*
 * File: FilteredDto.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Http\Requests\DTO;

use App\Http\Requests\DTO\Contracts\FilteredInterface;

class FilteredDto implements FilteredInterface
{
    public ?string $query;
    public bool $with_deleted = false;

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function toArray(): array
    {
        return (array)$this;
    }

    public function withDeleted(): bool
    {
        return $this->with_deleted;
    }
}