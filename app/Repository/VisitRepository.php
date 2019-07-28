<?php
/**
 * File: VisitRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\Api\DTO\Visit as VisitDto;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Visit;
use App\Models\User;

/**
 * Class VisitRepository
 * @package App\Repository
 */
class VisitRepository
{
    /**
     * @param int $id
     * @return Visit|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id)
    {
        return Visit::query()
            ->where('id', $id)
            ->firstOrFail();
    }

    /**
     * @param VisitDto $dto
     * @param User $user
     * @return Visit
     */
    public function createFromDto(VisitDto $dto, User $user): Visit
    {
        $visit = new Visit;
        $visit->manager_id = $user->id;
        $visit->student_id = $dto->student_id;
        $visit->event_id = $dto->event_id;
        $visit->event_type = $dto->event_type;
        $visit->payment_id = $dto->payment_id;
        $visit->payment_type = $dto->payment_type;
        $visit->save();

        return $visit;
    }

    /**
     * @param Lesson $lesson
     * @param Student $student
     * @param User $user
     * @return Visit
     */
    public function createLessonVisit(Lesson $lesson, Student $student, User $user): Visit
    {
        $visit = new Visit;
        $visit->manager_id = $user->id;
        $visit->student_id = $student->id;
        $visit->event_id = $lesson->id;
        $visit->event_type = Lesson::class;
        $visit->payment_id = null;
        $visit->payment_type = null;
        $visit->save();

        return $visit;
    }

    /**
     * @param Visit $visit
     * @throws \Exception
     */
    public function delete(Visit $visit): void
    {
        $visit->delete();
    }
}
