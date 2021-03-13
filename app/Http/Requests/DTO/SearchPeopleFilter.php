<?php
/*
 * File: SearchPeopleFilter.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 12.3.2021
 * Copyright (c) 2021
 */

namespace App\Http\Requests\DTO;

class SearchPeopleFilter
{
    public ?string $query;
    public ?\Carbon\Carbon $birth_date_from;
    public ?\Carbon\Carbon $birth_date_to;
    public ?string $gender;
}