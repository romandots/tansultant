<?php
/**
 * File: VisitRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\Api\DTO\LessonVisit as VisitDto;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Models\Visit;

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
     * @param User|null $user
     * @return Visit
     */
    public function createLessonVisitFromDto(VisitDto $dto, ?User $user = null): Visit
    {
        $visit = new Visit;
        $visit->student_id = $dto->student_id;
        $visit->event_id = $dto->lesson_id;
        $visit->event_type = Lesson::class;
        $visit->payment_type = Payment::class;
        $visit->payment_id = null;

        if (null !== $user) {
            $visit->manager_id = $user->id;
        }

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
