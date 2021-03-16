<?php
/*
 * File: PaginatedInterface.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

namespace App\Http\Requests\DTO\Contracts;

interface PaginatedInterface
{
    public function getMeta(int $totalRecords): array;
}