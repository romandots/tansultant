<?php
/**
 * File: CreatesFakeIntent.inc
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Intent;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\User;

/**
 * Trait CreatesFakeIntent
 * @package Tests\Traits
 */
trait CreatesFakeIntent
{
    /**
     * @param array|null $attributes
     * @param Lesson|null $lesson
     * @param Student|null $student
     * @param User|null $manager
     * @return Intent
     */
    private function createFakeIntent(
        ?array $attributes = [],
        ?Lesson $lesson = null,
        ?Student $student = null,
        ?User $manager = null
    ): Intent {
        if (!isset($attributes['event_type'])) {
            $attributes['event_type'] = Lesson::class;
        }

        if (!isset($attributes['event_id'])) {
            $lesson = $lesson ?? $this->createFakeLesson();
            $attributes['event_id'] = $lesson->id;
        }

        if (!isset($attributes['student_id'])) {
            $student = $student ?? $this->createFakeStudent();
            $attributes['student_id'] = $student->id;
        }

        if (!isset($attributes['manager_id'])) {
            $manager = $manager ?? $this->createFakeUser();
            $attributes['manager_id'] = $manager->id;
        }

        return \factory(Intent::class)->create($attributes);
    }
}
