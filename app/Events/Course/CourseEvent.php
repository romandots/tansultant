<?php
/**
 * File: BaseCourseEvent.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-20
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Events\Course;

use App\Events\BaseModelEvent;
use App\Models\Course;
use App\Models\Enum\LogRecordObjectType;
use App\Models\User;

abstract class CourseEvent extends BaseModelEvent
{

    public function __construct(
        public Course $course,
        public User $user,
    ) {
    }

    public function getType(): LogRecordObjectType
    {
        return LogRecordObjectType::COURSE;
    }

    public function getRecordId(): string
    {
        return $this->getCourse()->id;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function getUser(): User
    {
        return $this->user;
    }
    public static function created(Course $course, User $user): void
    {
        CourseCreatedEvent::dispatch($course, $user);
    }

    public static function deleted(Course $course, User $user): void
    {
        CourseDeletedEvent::dispatch($course, $user);
    }

    public static function disabled(Course $course, User $user): void
    {
        CourseDisabledEvent::dispatch($course, $user);
    }

    public static function enabled(Course $course, User $user): void
    {
        CourseEnabledEvent::dispatch($course, $user);
    }

    public static function restored(Course $course, User $user): void
    {
        CourseRestoredEvent::dispatch($course, $user);
    }

    public static function scheduleUpdated(Course $course, User $user): void
    {
        CourseScheduleUpdatedEvent::dispatch($course, $user);
    }

    public static function updated(Course $course, User $user): void
    {
        CourseUpdatedEvent::dispatch($course, $user);
    }
}