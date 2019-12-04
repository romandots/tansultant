<?php
/**
 * File: ScheduleRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\ManagerApi\DTO\StoreSchedule as ScheduleDto;
use App\Http\Requests\PublicApi\DTO\ScheduleOnDate;
use App\Models\Course;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class ScheduleRepository
 * @package App\Repository
 */
class ScheduleRepository
{
    /**
     * @param string $id
     * @return Schedule
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): Schedule
    {
        return Schedule::query()->findOrFail($id);
    }

    /**
     * @param ScheduleDto $dto
     * @return Schedule
     * @throws \Exception
     */
    public function create(ScheduleDto $dto): Schedule
    {
        $schedule = new Schedule;
        $schedule->id = \uuid();
        $this->fill($schedule, $dto);
        $schedule->save();

        return $schedule;
    }

    /**
     * @param Schedule $schedule
     * @param ScheduleDto $dto
     */
    public function update(Schedule $schedule, ScheduleDto $dto): void
    {
        $this->fill($schedule, $dto);
        $schedule->save();
    }

    /**
     * @param Schedule $schedule
     * @throws \Exception
     */
    public function delete(Schedule $schedule): void
    {
        $schedule->delete();
    }

    /**
     * @param ScheduleDto $dto
     * @param Schedule $schedule
     */
    private function fill(Schedule $schedule, ScheduleDto $dto): void
    {
        $schedule->branch_id = $dto->branch_id;
        $schedule->classroom_id = $dto->classroom_id;
        $schedule->course_id = $dto->course_id;
        $schedule->starts_at = $dto->starts_at;
        $schedule->ends_at = $dto->ends_at;
        $schedule->duration = $dto->duration;
        $schedule->monday = $dto->monday;
        $schedule->tuesday = $dto->tuesday;
        $schedule->wednesday = $dto->wednesday;
        $schedule->thursday = $dto->thursday;
        $schedule->friday = $dto->friday;
        $schedule->saturday = $dto->saturday;
        $schedule->sunday = $dto->sunday;
    }

    /**
     * @return Collection|Schedule[]
     */
    public function getAll(): Collection
    {
        return Schedule::query()->get();
    }

    /**
     * @param ScheduleOnDate $dto
     * @return Collection|Schedule[]
     */
    public function getSchedulesForDate(ScheduleOnDate $dto): Collection
    {
        $date = $dto->date;
        $weekDay = \mb_strtolower(\weekday($date));

        $query = DB::table(Schedule::TABLE)
            ->whereNotNull($weekDay)
            ->whereIn('course_id', static function (Builder $query) use ($date) {
                $query
                    ->select('id')
                    ->from(Course::TABLE)
                    ->where('status', '!=', Course::STATUS_DISABLED)
                    ->where(static function (Builder $query) use ($date) {
                        $query
                            ->whereNull('starts_at')
                            ->orWhere('starts_at', '<=', $date);
                    })
                    ->where(static function (Builder $query) use ($date) {
                        $query
                            ->whereNull('ends_at')
                            ->orWhere('ends_at', '>=', $date);
                    });
            });

        if (null !== $dto->course_id) {
            $query = $query->where('course_id', $dto->course_id);
        }

        if (null !== $dto->branch_id) {
            $query = $query->where('branch_id', $dto->branch_id);
        }

        if (null !== $dto->classroom_id) {
            $query = $query->where('classroom_id', $dto->classroom_id);
        }

        $results = $query
            ->distinct()
            ->orderBy($weekDay)
            ->get(Schedule::TABLE . '.*');

        return (new Schedule)->hydrate($results->toArray());
    }
}
