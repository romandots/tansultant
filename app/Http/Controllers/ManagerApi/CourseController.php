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
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseController
{
    protected CourseRepository $repository;
    protected CourseService $service;

    public function __construct(CourseRepository $repository, CourseService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(): AnonymousResourceCollection
    {
        $records = $this->repository->getAll();

        return CourseResource::collection($records);
    }

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
        $course = $this->service->create($request->getDto(), $request->user());

        return new CourseResource($course);
    }

    /**
     * @param UpdateCourseRequest $request
     * @param string $id
     * @return CourseResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(UpdateCourseRequest $request, string $id): CourseResource
    {
        $course = $this->repository->find($id);
        $this->service->update($course, $request->getDto(), $request->user());

        return new CourseResource($course);
    }

    /**
     * @param Request $request
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(Request $request, string $id): void
    {
        $course = $this->repository->find($id);
        $this->service->delete($course, $request->user());
    }

    /**
     * @param Request $request
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function disable(Request $request, string $id): void
    {
        $course = $this->repository->find($id);
        $this->service->disable($course, $request->user());
    }

    /**
     * @param Request $request
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function enable(Request $request, string $id): void
    {
        $course = $this->repository->find($id);
        $this->service->disable($course, $request->user());
    }
}
