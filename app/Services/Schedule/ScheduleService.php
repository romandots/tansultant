<?php
/**
 * File: ScheduleService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-25
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Schedule;

use App\Http\Requests\Api\DTO\ScheduleOnDate;
use App\Models\Schedule;
use App\Repository\ScheduleRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ScheduleService
 * @package App\Services\Schedule
 */
class ScheduleService
{
    /**
     * @var ScheduleRepository
     */
    private $repository;

    /**
     * @param ScheduleRepository $repository
     */
    public function __construct(ScheduleRepository $repository)
    {
        $this->repository = $repository;
    }
}
