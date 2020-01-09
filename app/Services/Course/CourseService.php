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
use Illuminate\Foundation\Auth\User;

/**
 * Class CourseService
 * @package App\Services\Course
 */
class CourseService
{
    private CourseRepository $repository;

    public function __construct(CourseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param StoreCourse $dto
     * @return Course
     * @param User $user
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function create(StoreCourse $dto, User $user): Course
    {
        $course = $this->repository->create($dto);
        $course->syncTagsWithType($dto->genres, Genre::class);
        $course->load('instructor');

        // $this->actions->log($user, $course, 'create');

        \event(new CourseCreatedEvent($course));

        return $course;
    }

    /**
     * @param Course $course
     * @param StoreCourse $dto
     * @param User $user
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(Course $course, StoreCourse $dto, User $user): void
    {
        $oldCourse = clone $course;
        $this->repository->update($course, $dto);
        $course->syncTagsWithType($dto->genres, Genre::class);
        $course->load('instructor');

        // $this->actions->log($user, $course, 'update', $oldCourse);

        \event(new CourseUpdatedEvent($course));
    }

    /**
     * Set course status to pending or active (according to working dates)
     *
     * @param Course $course
     * @param User $user
     */
    public function enable(Course $course, User $user): void
    {
        if ($course->isInPeriod()) {
            $this->repository->setActive($course);
        } else {
            $this->repository->setPending($course);
        }

        // $this->actions->log($user, $course, 'enable');

        \event(new CourseEnabledEvent($course));
    }

    /**
     * Set course status to disabled
     *
     * @param Course $course
     * @param User $user
     */
    public function disable(Course $course, User $user): void
    {
        if ($course->isInPeriod()) {
            $this->repository->setActive($course);
        } else {
            $this->repository->setPending($course);
        }

        // $this->actions->log($user, $course, 'disable');

        \event(new CourseDisabledEvent($course));
    }

    /**
     * @param Course $course
     * @param User $user
     * @throws \Exception
     */
    public function delete(Course $course, User $user): void
    {
        $this->repository->delete($course);

        // $this->actions->log($user, $course, 'delete');

        \event(new CourseDeletedEvent($course));
    }
}
