<?php
/**
 * File: CourseController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\StoreCourseRequest as UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Repository\CourseRepository;
use App\Services\Course\CourseService;

/**
 * Class CourseController
 * @package App\Http\Controllers\Api
 */
class CourseController
{
    /**
     * @var CourseRepository
     */
    protected $repository;

    /**
     * @var CourseService
     */
    protected $service;

    /**
     * CourseController constructor.
     * @param CourseRepository $repository
     * @param CourseService $service
     */
    public function __construct(CourseRepository $repository, CourseService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @param int $id
     * @return CourseResource
     */
    public function show(int $id): CourseResource
    {
        $course = $this->repository->find($id);
        $course->load('instructor');

        return new CourseResource($course);
    }

    /**
     * @param StoreCourseRequest $request
     * @return CourseResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function store(StoreCourseRequest $request): CourseResource
    {
        $course = $this->repository->create($request->getDto());
        $course->load('instructor');

        return new CourseResource($course);
    }

    /**
     * @param UpdateCourseRequest $request
     * @param int $id
     * @return CourseResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(UpdateCourseRequest $request, int $id): CourseResource
    {
        $course = $this->repository->find($id);
        $this->repository->update($course, $request->getDto());
        $course->load('instructor');

        return new CourseResource($course);
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function destroy(int $id): void
    {
        $course = $this->repository->find($id);
        $this->repository->delete($course);
    }
}
