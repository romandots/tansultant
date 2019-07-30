<?php
/**
 * File: CreatesFakeVisit.inc
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Lesson;
use App\Models\Student;
use App\Models\User;
use App\Models\Visit;

/**
 * Trait CreatesFakeVisit
 * @package Tests\Traits
 */
trait CreatesFakeVisit
{
    /**
     * @param Lesson|null $lesson
     * @param Student|null $student
     * @param User|null $manager
     * @param array|null $attributes
     * @return Visit
     */
    private function createFakeLessonVisit(
        ?Lesson $lesson = null,
        ?Student $student = null,
        ?User $manager = null,
        ?array $attributes = []
    ): Visit {
        $attributes['event_type'] = Lesson::class;
        $lesson = $lesson ?: $this->createFakeLesson();
        $attributes['event_id'] = $lesson->id;
        $student = $student ?: $this->createFakeStudent();
        $attributes['student_id'] = $student->id;
        $manager = $manager ?: $this->createFakeUser();
        $attributes['manager_id'] = $manager->id;
        $attributes['payment_id'] = null;

        return \factory(Visit::class)->create($attributes);
    }
}
