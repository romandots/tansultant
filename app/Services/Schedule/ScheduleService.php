<?php
/**
 * File: ScheduleService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-25
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Schedule;

use App\Events\Course\CourseScheduleUpdatedEvent;
use App\Http\Requests\ManagerApi\DTO\StoreSchedule;
use App\Models\Schedule;
use App\Models\User;
use App\Repository\Exceptions\ScheduleSlotIsOccupied;
use App\Repository\ScheduleRepository;

/**
 * Class ScheduleService
 * @package App\Services\Schedule
 */
class ScheduleService
{
    /**
     * @var ScheduleRepository
     */
    private ScheduleRepository $repository;

    /**
     * @param ScheduleRepository $repository
     */
    public function __construct(ScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param StoreSchedule $store
     * @return Schedule
     * @throws ScheduleSlotIsOccupied
     * @throws \Exception
     */
    public function create(StoreSchedule $store): Schedule
    {
        $this->repository->checkSpace($store);

        $schedule = $this->repository->create($store);
        $schedule->load('course', 'branch', 'classroom');

        \event(new CourseScheduleUpdatedEvent($schedule->course, $store->user));

        return $schedule;
    }

    /**
     * @param Schedule $schedule
     * @param StoreSchedule $update
     */
    public function update(Schedule $schedule, StoreSchedule $update): void
    {
        $this->repository->checkSpace($update);
        $this->repository->update($schedule, $update);

        \event(new CourseScheduleUpdatedEvent($schedule->course, $update->user));
    }

    /**
     * @param Schedule $schedule
     * @param User $user
     * @throws \Exception
     */
    public function delete(Schedule $schedule, User $user): void
    {
        $this->repository->delete($schedule);

        \event(new CourseScheduleUpdatedEvent($schedule->course, $user));
    }
}
