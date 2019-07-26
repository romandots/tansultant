<?php
/**
 * File: LessonRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\Api\DTO\Lesson as LessonDto;
use App\Http\Requests\Api\DTO\LessonsOnDate;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class LessonRepository
 * @package App\Repository
 */
class LessonRepository
{
    /**
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|Lesson
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): Lesson
    {
        return Lesson::query()->findOrFail($id);
    }

    /**
     * @param $name
     * @param LessonDto $dto
     * @return Lesson
     */
    public function create(string $name, LessonDto $dto): Lesson
    {
        $lesson = new Lesson;
        $lesson->name = $name;
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
     * @param LessonDto $dto
     */
    private function fill(Lesson $lesson, LessonDto $dto): void
    {
        $lesson->branch_id = $dto->branch_id;
        $lesson->classroom_id = $dto->classroom_id;
        $lesson->course_id = $dto->course_id;
        $lesson->instructor_id = $dto->instructor_id;
        $lesson->starts_at = $dto->starts_at;
        $lesson->ends_at = $dto->ends_at;
        $lesson->type = $dto->type;
    }

    /**
     * @param Lesson $lesson
     * @throws \Exception
     */
    public function delete(Lesson $lesson): void
    {
        $lesson->delete();
    }

    /**
     * @param LessonsOnDate $dto
     * @return Collection|Lesson[]
     */
    public function getLessonsForDate(LessonsOnDate $dto): Collection
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
            ->get();
    }
}
