<?php
/**
 * File: CourseService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Course;

use App\Events\CourseCreatedEvent;
use App\Events\CourseDeletedEvent;
use App\Events\CourseDisabledEvent;
use App\Events\CourseEnabledEvent;
use App\Events\CourseUpdatedEvent;
use App\Http\Requests\ManagerApi\DTO\StoreCourse;
use App\Models\Course;
use App\Models\Genre;
use App\Repository\CourseRepository;
use App\Services\LogRecord\LogRecordService;
use Illuminate\Foundation\Auth\User;

/**
 * Class CourseService
 * @package App\Services\Course
 */
class CourseService
{
    private CourseRepository $repository;

    private LogRecordService $actions;

    public function __construct(CourseRepository $repository, LogRecordService $actions)
    {
        $this->repository = $repository;
        $this->actions = $actions;
    }

    /**
     * @param StoreCourse $dto
     * @param User $user
     * @return Course
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     */
    public function create(StoreCourse $dto, User $user): Course
    {
        $course = $this->repository->create($dto);
        $course->syncTagsWithType($dto->genres, Genre::class);
        $course->load('instructor');

        $this->actions->logCreate($user, $course);

        \event(new CourseCreatedEvent($course));

        return $course;
    }

    /**
     * @param Course $course
     * @param StoreCourse $dto
     * @param User $user
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     */
    public function update(Course $course, StoreCourse $dto, User $user): void
    {
        $oldCourse = clone $course;
        $this->repository->update($course, $dto);
        $course->syncTagsWithType($dto->genres, Genre::class);
        $course->load('instructor');

        $this->actions->logUpdate($user, $course, $oldCourse);

        \event(new CourseUpdatedEvent($course));
    }

    /**
     * Set course status to pending or active (according to working dates)
     *
     * @param Course $course
     * @param User $user
     * @throws \Exception
     */
    public function enable(Course $course, User $user): void
    {
        $oldCourse = clone $course;

        if ($course->isInPeriod()) {
            $this->repository->setActive($course);
        } else {
            $this->repository->setPending($course);
        }

         $this->actions->logEnable($user, $course, $oldCourse);

        \event(new CourseEnabledEvent($course));
    }

    /**
     * Set course status to disabled
     *
     * @param Course $course
     * @param User $user
     * @throws \Exception
     */
    public function disable(Course $course, User $user): void
    {
        $oldCourse = clone $course;

        if ($course->isInPeriod()) {
            $this->repository->setActive($course);
        } else {
            $this->repository->setPending($course);
        }

        $this->actions->logDisable($user, $course, $oldCourse);

        \event(new CourseDisabledEvent($course));
    }

    /**
     * @param Course $course
     * @param User $user
     * @throws \Exception
     */
    public function delete(Course $course, User $user): void
    {
        $oldCourse = clone $course;

        $this->repository->delete($course);

        $this->actions->logDelete($user, $oldCourse);

        \event(new CourseDeletedEvent($course));
    }
}
