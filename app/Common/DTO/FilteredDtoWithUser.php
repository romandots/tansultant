<?php
/*
 * File: FilteredDto.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Common\DTO;

use App\Common\Contracts\FilteredInterface;

class FilteredDtoWithUser implements FilteredInterface, \App\Common\Contracts\DtoWithUser
{
    public ?\App\Models\User $user;
    public ?string $query;
    public bool $with_deleted = false;

    public function __construct(?\App\Models\User $user = null)
    {
        $this->user = $user;
    }

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

    public function getUser(): \App\Models\User
    {
        return $this->user;
    }
}