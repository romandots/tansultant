<?php
/**
 * File: IntentRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\ManagerApi\DTO\StoreIntent as IntentDto;
use App\Models\Intent;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\User;

/**
 * Class IntentRepository
 * @package App\Repository
 */
class IntentRepository
{
    /**
     * @param string $id
     * @return Intent|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id)
    {
        return Intent::query()
            ->where('id', $id)
            ->firstOrFail();
    }

    /**
     * @param IntentDto $dto
     * @param User $user
     * @return Intent
     * @throws \Exception
     */
    public function createFromDto(IntentDto $dto, User $user): Intent
    {
        $intent = new Intent;
        $intent->id = \uuid();
        $intent->manager_id = $user->id;
        $intent->status = Intent::STATUS_EXPECTING;
        $intent->student_id = $dto->student_id;
        $intent->event_id = $dto->event_id;
        $intent->event_type = $dto->event_type;
        $intent->save();

        return $intent;
    }

    /**
     * @param Lesson $lesson
     * @param Student $student
     * @param User $user
     * @return Intent
     * @throws \Exception
     */
    public function createLessonIntent(Lesson $lesson, Student $student, User $user): Intent
    {
        $intent = new Intent;
        $intent->id = \uuid();
        $intent->manager_id = $user->id;
        $intent->status = Intent::STATUS_EXPECTING;
        $intent->student_id = $student->id;
        $intent->event_id = $lesson->id;
        $intent->event_type = Lesson::class;
        $intent->save();

        return $intent;
    }

    /**
     * @param Intent $intent
     * @throws \Exception
     */
    public function delete(Intent $intent): void
    {
        $intent->delete();
    }

    /**
     * @param Intent $intent
     */
    public function setVisited(Intent $intent): void
    {
        $intent->status = Intent::STATUS_VISITED;
        $intent->save();
    }

    /**
     * @param Intent $intent
     */
    public function setNoShow(Intent $intent): void
    {
        $intent->status = Intent::STATUS_NOSHOW;
        $intent->save();
    }
}
