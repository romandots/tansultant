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
     * @param Lesson|null $lesson
     * @param Student|null $student
     * @param User|null $manager
     * @param array|null $attributes
     * @return Intent
     */
    private function createFakeLessonIntent(
        ?Lesson $lesson = null,
        ?Student $student = null,
        ?User $manager = null,
        ?array $attributes = []
    ): Intent {
        $attributes['event_type'] = Lesson::class;
        if (null !== $lesson) {
            $attributes['event_id'] = $lesson->id;
        }
        if (null !== $student) {
            $attributes['student_id'] = $student->id;
        }
        if (null !== $manager) {
            $attributes['manager_id'] = $manager->id;
        }

        return \factory(Intent::class)->create($attributes);
    }
}
