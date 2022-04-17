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
use App\Http\Requests\ManagerApi\DTO\ScheduleOnDate;
use App\Models\Course;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ScheduleRepository
 * @package App\Repository
 */
class ScheduleRepository
{
    /**
     * @param string $courseId
     * @return Collection<Schedule>
     */
    public function getAllByCourseId(string $courseId): Collection
    {
        return Schedule::query()
            ->where('course_id', $courseId)
            ->whereNull('deleted_at')
            ->with('branch', 'classroom')
            ->get();
    }

    /**
     * @return Collection<Schedule>
     */
    public function getAll(): Collection
    {
        return Schedule::query()
            ->whereNull('deleted_at')
            ->with('course')
            ->get();
    }

    /**
     * @param string $id
     * @return Schedule
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): Schedule
    {
        return Schedule::query()
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->firstOrFail();
    }

    /**
     * @param string $scheduleId
     * @return Schedule
     */
    public function findById(string $scheduleId): Schedule
    {
        return Schedule::query()
            ->whereNull('deleted_at')
//            ->where('course_id', $courseId)
            ->where('id', $scheduleId)
            ->firstOrFail();
    }

    /**
     * @param string $id
     * @return Schedule|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findDeleted(string $id): Schedule
    {
        return Schedule::query()
            ->whereNotNull('deleted_at')
            ->where('id', $id)
            ->firstOrFail();
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
        $schedule->created_at = Carbon::now();
        $schedule->updated_at = Carbon::now();

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
        $schedule->updated_at = Carbon::now();

        $this->fill($schedule, $dto);
        $schedule->save();
    }

    /**
     * @param Schedule $schedule
     * @throws \Exception
     */
    public function delete(Schedule $schedule): void
    {
        $schedule->deleted_at = Carbon::now();
        $schedule->updated_at = Carbon::now();
        $schedule->save();
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
        $schedule->cycle = $dto->cycle;
        $schedule->weekday = $dto->weekday;
        $schedule->from_date = $dto->from_date;
        $schedule->to_date = $dto->to_date;
        $schedule->starts_at = $dto->starts_at;
        $schedule->ends_at = $dto->ends_at;
    }

    /**
     * Get schedules for
     *  courses in NOT disabled status
     *  for selected date
     *  by course_id (optional)
     *  classroom_id (optional)
     *  branch_id (optional)
     * @param ScheduleOnDate $dto
     * @param string[]|null $relations
     * @return Collection<Schedule>
     */
    public function getSchedulesForDateWithRelations(ScheduleOnDate $dto, ?array $relations = []): Collection
    {
        $query = Schedule::query()//table(Schedule::TABLE)
        ->where('weekday', $dto->weekday)
            ->whereIn(
                'course_id',
                static function (Builder $query) use ($dto) {
                    $query
                        ->select('id')
                        ->from(Course::TABLE)
                        ->where('status', '=', Course::STATUS_ACTIVE)
                        ->where(
                            static function (Builder $query) use ($dto) {
                                $query
                                    ->whereNull('starts_at')
                                    ->orWhere('starts_at', '<=', $dto->date);
                            }
                        )
                        ->where(
                            static function (Builder $query) use ($dto) {
                                $query
                                    ->whereNull('ends_at')
                                    ->orWhere('ends_at', '>=', $dto->date);
                            }
                        );
                }
            );

        if (null !== $dto->course_id) {
            $query = $query->where('course_id', $dto->course_id);
        }

        if (null !== $dto->branch_id) {
            $query = $query->where('branch_id', $dto->branch_id);
        }

        if (null !== $dto->classroom_id) {
            $query = $query->where('classroom_id', $dto->classroom_id);
        }

        if ([] !== $relations) {
            $query->with($relations);
        }

        return $query
            ->distinct()
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * @param Schedule $schedule
     */
    public function restore(Schedule $schedule): void
    {
        $schedule->deleted_at = null;
        $schedule->updated_at = Carbon::now();
        $schedule->save();
    }

    /**
     * @param string $courseId
     * @param Carbon $date
     * @return Collection<Schedule>
     */
    public function getSchedulesForCourseOnDate(string $courseId, Carbon $date): Collection
    {
        return $this->getQueryForDate($date)
            ->where('course_id', $courseId)
            ->get();
    }

    /**
     * @param Carbon $date
     * @return Collection<Schedule>
     */
    public function getSchedulesOnDate(Carbon $date): Collection
    {
        return $this->getQueryForDate($date)
            ->get();
    }

    private function getQueryForDate($date): Builder
    {
        return Schedule::query()
            ->whereNull('deleted_at')
            ->where(function (Builder $query) use ($date) {
                $query
                    ->where('from_date', '<=', $date->toDateString())
                    ->orWhereNull('from_date');
            })
            ->where(function (Builder $query) use ($date) {
                $query
                    ->where('to_date', '>', $date->toDateString())
                    ->orWhereNull('to_date');
            })
            ->where(function (Builder $query) use ($date) {
                $query
                    ->where(function (Builder $query) use ($date) {
                        $query
                            ->where('cycle', Schedule::CYCLE_ONCE)
                            ->where('from_date', $date->toDateString());
                    })
                    ->orWhere(function (Builder $query) use ($date) {
                        $query
                            ->where('cycle', Schedule::CYCLE_EVERY_WEEK)
                            ->where('weekday', (string)$date->dayOfWeekIso);
                    })
                    ->orWhere(function (Builder $query) use ($date) {
                        $query
                            ->where('cycle', Schedule::CYCLE_EVERY_MONTH)
                            ->whereRaw('EXTRACT (DAY FROM from_date::date) = ?', [$date->day]);
                    })
                    ->orWhere(function (Builder $query) {
                        $query
                            ->where('cycle', Schedule::CYCLE_EVERY_DAY);
                    });
            });
    }
}
