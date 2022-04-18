<?php
/*
 * File: FilteredInterface.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Common\Contracts;

interface FilteredInterface
{
    public function getQuery(): ?string;

    public function toArray(): array;

    public function withDeleted(): bool;
}