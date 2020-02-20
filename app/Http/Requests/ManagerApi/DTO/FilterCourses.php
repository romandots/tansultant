<?php
/**
 * File: FilterCourses.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-20
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

class FilterCourses
{
    public array $statuses;
    public array $instructors_ids;
    public int $page;
    public int $perPage;
    public string $sort;
    public string $order;
}