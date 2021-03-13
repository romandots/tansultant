<?php
/*
 * File: SearchPeople.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 12.3.2021
 * Copyright (c) 2021
 */

declare(strict_types=1);

namespace App\Http\Requests\DTO;

/**
 * Class StoreInstructor
 * @package App\Http\Requests\ManagerApi\DTO
 */
class SearchPeople
{
    public SearchPeopleFilter $filter;
    public int $offset;
    public int $limit;
    public string $sort;
    public string $order;
}