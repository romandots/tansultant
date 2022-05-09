<?php

declare(strict_types=1);

namespace App\Components\Schedule;

use App\Http\Requests\ManagerApi\DTO\ScheduleOnDate;
use App\Models\Course;
use App\Models\Enum\CourseStatus;
use App\Models\Enum\ScheduleCycle;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Schedule make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Schedule> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Schedule find(string $id)
 * @method Schedule findTrashed(string $id)
 * @method Schedule create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Schedule $record)
 * @method void restore(Schedule $record)
 * @method void forceDelete(Schedule $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Schedule::class,
            ['name']
        );
    }

    /**
     * @param Schedule $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->branch_id = $dto->branch_id;
        $record->classroom_id = $dto->classroom_id;
        $record->course_id = $dto->course_id;
        $record->cycle = $dto->cycle;
        $record->weekday = $dto->weekday;
        $record->from_date = $dto->from_date;
        $record->to_date = $dto->to_date;
        $record->starts_at = $dto->starts_at;
        $record->ends_at = $dto->ends_at;
    }

    /**
     * @param string $courseId
     * @return Collection<Schedule>
     */
    public function getAllByCourseId(string $courseId): Collection
    {
        return $this->getQuery()
            ->where('course_id', $courseId)
            ->whereNull('deleted_at')
            ->with('branch', 'classroom')
            ->get();
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
        $query = $this->getQuery()
        ->where('weekday', $dto->weekday)
            ->whereIn(
                'course_id',
                static function (Builder $query) use ($dto) {
                    $query
                        ->select('id')
                        ->from(Course::TABLE)
                        ->where('status', '=', CourseStatus::ACTIVE)
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

    protected function getQueryForDate($date): Builder
    {
        return $this->getQuery()
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
                            ->where('cycle', ScheduleCycle::ONCE)
                            ->where('from_date', $date->toDateString());
                    })
                    ->orWhere(function (Builder $query) use ($date) {
                        $query
                            ->where('cycle', ScheduleCycle::EVERY_WEEK)
                            ->where('weekday', (string)$date->dayOfWeekIso);
                    })
                    ->orWhere(function (Builder $query) use ($date) {
                        $query
                            ->where('cycle', ScheduleCycle::EVERY_MONTH)
                            ->whereRaw('EXTRACT (DAY FROM from_date::date) = ?', [$date->day]);
                    })
                    ->orWhere(function (Builder $query) {
                        $query
                            ->where('cycle', ScheduleCycle::EVERY_DAY);
                    });
            });
    }
}