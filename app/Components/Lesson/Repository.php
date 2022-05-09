<?php

declare(strict_types=1);

namespace App\Components\Lesson;

use App\Http\Requests\ManagerApi\DTO\LessonsFiltered;
use App\Models\Enum\LessonStatus;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Lesson make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Lesson> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Lesson find(string $id)
 * @method Lesson findTrashed(string $id)
 * @method Lesson create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Lesson $record)
 * @method void restore(Lesson $record)
 * @method void forceDelete(Lesson $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Lesson::class,
            ['name']
        );
    }

    /**
     * @param Lesson $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->branch_id = $dto->branch_id;
        $record->course_id = $dto->course_id;
        $record->schedule_id = $dto->schedule_id;
        $record->classroom_id = $dto->classroom_id;
        $record->instructor_id = $dto->instructor_id;
        $record->controller_id = $dto->controller_id;
        $record->payment_id = $dto->payment_id;
        $record->status = $dto->status;
        $record->starts_at = $dto->starts_at;
        $record->ends_at = $dto->ends_at;
        $record->type = $dto->type;
    }

    /**
     * @param Carbon $date
     * @param array $relations
     * @return Collection<Lesson>
     */
    public function getLessonsOnDate(Carbon $date, array $relations = []): Collection
    {
        return $this->getQuery()
            ->whereNull('deleted_at')
            ->whereRaw('DATE(starts_at) = ?', [$date->toDateString()])
            ->distinct()
            ->orderBy('starts_at')
            ->get()
            ->load($relations);
    }

    /**
     * @param LessonsFiltered $dto
     * @param array $relations
     * @return Collection<Lesson>
     */
    public function getLessonsFiltered(LessonsFiltered $dto, array $relations = []): Collection
    {
        $query = $this->getQuery()
            ->whereRaw('DATE(starts_at) = ?', [$dto->date]);

        if (null !== $dto->course_id) {
            $query = $query->where('course_id', $dto->course_id);
        }

        if (null !== $dto->branch_id) {
            $query = $query->where('branch_id', $dto->branch_id);
        }

        if (null !== $dto->classroom_id) {
            $query = $query->where('classroom_id', $dto->classroom_id);
        }

        return $query
            ->distinct()
            ->orderBy('starts_at')
            ->get()
            ->load($relations);
    }

    /**
     * @param Lesson $lesson
     */
    public function close(Lesson $lesson): void
    {
        $lesson->status = LessonStatus::CLOSED;
        $lesson->updated_at = Carbon::now();
        $lesson->closed_at = Carbon::now();
        $lesson->save();
    }

    /**
     * @param Lesson $lesson
     */
    public function open(Lesson $lesson): void
    {
        $lesson->status = LessonStatus::PASSED;
        $lesson->updated_at = Carbon::now();
        $lesson->closed_at = null;
        $lesson->save();
    }

    /**
     * @param Lesson $lesson
     */
    public function cancel(Lesson $lesson): void
    {
        $lesson->status = LessonStatus::CANCELED;
        $lesson->updated_at = Carbon::now();
        $lesson->canceled_at = Carbon::now();
        $lesson->save();
    }

    /**
     * @param Lesson $lesson
     */
    public function book(Lesson $lesson): void
    {
        $lesson->status = LessonStatus::BOOKED;
        $lesson->updated_at = Carbon::now();
        $lesson->canceled_at = null;
        $lesson->save();
    }

    public function checkIfScheduleLessonExist(string $scheduleId, string $startTimeStamp, string $endTimeStamp): bool
    {
        return $this->getQuery()
            ->where('schedule_id', $scheduleId)
            ->where('starts_at', $startTimeStamp)
            ->where('ends_at', $endTimeStamp)
            ->exists();
    }

}