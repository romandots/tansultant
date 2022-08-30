<?php
/*
 * File: FilteredPaginatedDto.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 14.3.2021
 * Copyright (c) 2021
 */

declare(strict_types=1);

namespace App\Common\DTO;

class ShowDto extends DtoWithUser
{
    public string $id;
    public array $with = [];
    public array $with_count = [];
}