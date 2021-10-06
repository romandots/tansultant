<?php

namespace App\Services\Schedule;

use App\Http\Requests\ManagerApi\DTO\StoreSchedule;
use App\Http\Requests\PublicApi\DTO\ScheduleOnDate;
use App\Models\Schedule;
use App\Models\User;
use App\Repository\ScheduleRepository;
use Illuminate\Database\Eloquent\Collection;
use JetBrains\PhpStorm\Pure;

class ScheduleFacade
{
    private ScheduleService $service;
    private ScheduleManager $manager;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->service = $scheduleService;
    }

    public function getService(): ScheduleService
    {
        return $this->service;
    }

    public function getManager(): ScheduleManager
    {
        return $this->manager;
    }

    #[Pure]
    public function getRepository(): ScheduleRepository
    {
        return $this->service->getRepository();
    }

    public function findAndUpdate(string $scheduleId, StoreSchedule $storeSchedule): void
    {
        $schedule = $this->getRepository()->findById($scheduleId);
        $this->service->update($schedule, $storeSchedule);
    }

    /**
     * @param string $scheduleId
     * @param string $courseId
     * @param User $user
     * @throws \Exception
     */
    public function findAndDelete(string $scheduleId, User $user): void
    {
        $schedule = $this->getRepository()->findById($scheduleId);
        $this->service->delete($schedule, $user);
    }

    /**
     * @param StoreSchedule $store
     * @return Schedule
     * @throws \Exception
     */
    public function create(StoreSchedule $store): Schedule
    {
        return $this->service->create($store);
    }

    /**
     * @param Schedule $schedule
     * @param StoreSchedule $update
     */
    public function update(Schedule $schedule, StoreSchedule $update): void
    {
        $this->service->update($schedule, $update);
    }

    /**
     * @param Schedule $schedule
     * @param User $user
     * @throws \Exception
     */
    public function delete(Schedule $schedule, User $user): void
    {
        $this->service->delete($schedule, $user);
    }

    /**
     * @param string $courseId
     * @return \Illuminate\Database\Eloquent\Collection<Schedule>
     */
    public function getAllByCourseId(string $courseId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getRepository()->getAllByCourseId($courseId);
    }

    /**
     * @param ScheduleOnDate $scheduleOnDate
     * @param array $relations (optional)
     * @return Collection<Schedule>
     */
    public function getSchedulesForDateWithRelations(ScheduleOnDate $scheduleOnDate, array $relations = []): Collection
    {
        return $this->getRepository()->getSchedulesForDateWithRelations($scheduleOnDate, $relations);
    }
}