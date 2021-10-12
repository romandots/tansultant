<?php
/**
 * File: LessonRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\ManagerApi\DTO\LessonsFiltered;
use App\Http\Requests\ManagerApi\DTO\SearchLessonsFilterDto;
use App\Http\Requests\ManagerApi\DTO\StoreLesson as LessonDto;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class LessonRepository
 * @package App\Repository
 */
class LessonRepository extends BaseRepository
{
    public const SEARCHABLE_ATTRIBUTES = ['name'];

    /**
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Model|Lesson
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): Lesson
    {
        return Lesson::query()->findOrFail($id);
    }

    /**
     * @param LessonDto $dto
     * @return Lesson
     * @throws \Exception
     */
    public function create(LessonDto $dto): Lesson
    {
        $lesson = new Lesson;
        $lesson->id = \uuid();
        $lesson->name = $dto->name;
        $lesson->schedule_id = $dto->schedule_id;
        $lesson->course_id = $dto->course_id;
        $lesson->status = Lesson::STATUS_BOOKED;
        $this->fill($lesson, $dto);
        $lesson->save();

        return $lesson;
    }

    /**
     * @param Lesson $lesson
     * @param LessonDto $dto
     */
    public function update(Lesson $lesson, LessonDto $dto): void
    {
        $this->fill($lesson, $dto);
        $lesson->save();
    }

    /**
     * @param Lesson $lesson
     * @param string $instructorId
     */
    public function updateInstructor(Lesson $lesson, string $instructorId): void
    {
        $lesson->instructor_id = $instructorId;
        $lesson->save();
    }

    /**
     * @param Lesson $lesson
     * @param LessonDto $dto
     */
    private function fill(Lesson $lesson, LessonDto $dto): void
    {
        $lesson->branch_id = $dto->branch_id;
        $lesson->classroom_id = $dto->classroom_id;
        $lesson->instructor_id = $dto->instructor_id;
        $lesson->starts_at = $dto->starts_at;
        $lesson->ends_at = $dto->ends_at;
        $lesson->type = $dto->type;
    }

    /**
     * @param Carbon $date
     * @param array $relations
     * @return Collection<Lesson>
     */
    public function getLessonsOnDate(Carbon $date, array $relations = []): Collection
    {
        return Lesson::query()
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
        $query = Lesson::query()
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
        $lesson->status = Lesson::STATUS_CLOSED;
        $lesson->closed_at = Carbon::now();
        $lesson->save();
    }

    /**
     * @param Lesson $lesson
     */
    public function open(Lesson $lesson): void
    {
        $lesson->status = Lesson::STATUS_PASSED;
        $lesson->closed_at = null;
        $lesson->save();
    }

    /**
     * @param Lesson $lesson
     */
    public function cancel(Lesson $lesson): void
    {
        $lesson->status = Lesson::STATUS_CANCELED;
        $lesson->canceled_at = Carbon::now();
        $lesson->save();
    }

    /**
     * @param Lesson $lesson
     */
    public function book(Lesson $lesson): void
    {
        $lesson->status = Lesson::STATUS_BOOKED;
        $lesson->canceled_at = null;
        $lesson->save();
    }

    public function checkIfScheduleLessonExist(string $scheduleId, string $startTimeStamp, string $endTimeStamp): bool
    {
        return Lesson::query()
            ->where('schedule_id', $scheduleId)
            ->where('starts_at', $startTimeStamp)
            ->where('ends_at', $endTimeStamp)
            ->exists();
    }

    public function getSearchableAttributes(): array
    {
        return self::SEARCHABLE_ATTRIBUTES;
    }

    public function withSoftDeletes(): bool
    {
        return true;
    }

    public function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Lesson::query();
    }
}
