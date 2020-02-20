<?php
/**
 * File: CourseService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Course;

use App\Http\Requests\ManagerApi\DTO\StoreCourse;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use App\Repository\CourseRepository;

/**
 * Class CourseService
 * @package App\Services\Course
 */
class CourseService
{
    protected CourseRepository $repository;

    public function __construct(CourseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param StoreCourse $store
     * @return Course
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function create(StoreCourse $store): Course
    {
        $this->checkInstructorStatus($store);
        $course = $this->repository->create($store);
        \event(new \App\Events\Course\CourseCreatedEvent($course, $store->user));

        return $course;
    }

    /**
     * @param Course $course
     * @param StoreCourse $update
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(Course $course, StoreCourse $update): void
    {
        $this->checkInstructorStatus($update);
        $this->repository->update($course, $update);
        \event(new \App\Events\Course\CourseUpdatedEvent($course, $update->user));
    }

    public function delete(Course $course, User $user): void
    {
        $this->repository->delete($course);
        \event(new \App\Events\Course\CourseDeletedEvent($course, $user));
    }

    public function restore(Course $course, User $user): void
    {
        $this->repository->restore($course);
        \event(new \App\Events\Course\CourseRecoveredEvent($course, $user));
    }

    public function enable(Course $course, User $user): void
    {
        $this->repository->enable($course);
        \event(new \App\Events\Course\CourseEnabledEvent($course, $user));
    }

    public function disable(Course $course, User $user): void
    {
        $this->repository->disable($course);
        \event(new \App\Events\Course\CourseDisabledEvent($course, $user));
    }

    private function checkInstructorStatus(StoreCourse $course): void
    {
        if (Instructor::STATUS_FIRED === $course->instructor->status) {
            throw new Exceptions\InstructorStatusIncompatible($course->instructor);
        }
    }
}
