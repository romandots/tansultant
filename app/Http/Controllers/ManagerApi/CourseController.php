<?php
/**
 * File: CourseController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Requests\ManagerApi\StoreCourseRequest;
use App\Http\Requests\ManagerApi\StoreCourseRequest as UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Repository\CourseRepository;
use App\Services\Course\CourseService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $records = $this->repository->getAll();

        return CourseResource::collection($records);
    }

    /**
     * @param string $id
     * @return CourseResource
     */
    public function show(string $id): CourseResource
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
     * @param string $id
     * @return CourseResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(UpdateCourseRequest $request, string $id): CourseResource
    {
        $course = $this->repository->find($id);
        $this->repository->update($course, $request->getDto());
        $course->load('instructor');

        return new CourseResource($course);
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $course = $this->repository->find($id);
        $this->repository->delete($course);
    }
}
