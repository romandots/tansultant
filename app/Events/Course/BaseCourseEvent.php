<?php
/**
 * File: BaseCourseEvent.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-20
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Events\Course;

use App\Events\BaseEvent;
use App\Models\Course;
use App\Models\User;

class BaseCourseEvent extends BaseEvent implements CourseEventInterface
{
    public Course $course;
    public User $user;

    public function __construct(Course $course, User $user)
    {
        $this->course = $course;
        $this->user = $user;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}