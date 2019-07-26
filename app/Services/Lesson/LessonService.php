<?php
/**
 * File: LessonService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Http\Requests\Api\DTO\Lesson as LessonDto;
use App\Models\Lesson;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;

/**
 * Class LessonService
 * @package App\Services\Lesson
 */
class LessonService
{
    /**
     * @var LessonRepository
     */
    private $repository;

    /**
     * @var CourseRepository
     */
    private $courseRepository;

    /**
     * LessonController constructor.
     * @param LessonRepository $repository
     * @param CourseRepository $courseRepository
     */
    public function __construct(LessonRepository $repository, CourseRepository $courseRepository)
    {
        $this->repository = $repository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * @param LessonDto $dto
     * @return Lesson
     */
    public function create(LessonDto $dto): Lesson
    {
        switch ($dto->type) {
            case Lesson::TYPE_LESSON:
                $course = $this->courseRepository->find($dto->course_id);
                $name = \sprintf(
                    '%s %s',
                    \trans('lesson.' . Lesson::TYPE_LESSON),
                    $course->name
                );
                if (null === $dto->instructor_id) {
                    $dto->instructor_id = $course->instructor_id;
                }
                break;
            default:
                $name = \trans('lesson.' . Lesson::TYPE_LESSON);
        }

        return $this->repository->create($name, $dto);
    }
}
