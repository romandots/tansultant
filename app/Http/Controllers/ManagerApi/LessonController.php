<?php
/**
 * File: LessonController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Requests\ManagerApi\ChangeLessonInstructorRequest;
use App\Http\Requests\ManagerApi\LessonsOnDateRequest;
use App\Http\Requests\ManagerApi\StoreLessonRequest;
use App\Http\Requests\ManagerApi\StoreLessonRequest as UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Repository\LessonRepository;
use App\Services\Lesson\LessonService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class LessonController
 * @package App\Http\Controllers\Api
 */
class LessonController
{
    /**
     * @var LessonRepository
     */
    protected $repository;

    /**
     * @var LessonService
     */
    protected $service;

    /**
     * LessonController constructor.
     * @param LessonRepository $repository
     * @param LessonService $service
     */
    public function __construct(LessonRepository $repository, LessonService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @param string $id
     * @return LessonResource
     */
    public function show(string $id): LessonResource
    {
        $lesson = $this->repository->find($id);
        $lesson->load('instructor', 'course', 'controller');

        return new LessonResource($lesson);
    }

    /**
     * @param StoreLessonRequest $request
     * @return LessonResource
     */
    public function store(StoreLessonRequest $request): LessonResource
    {
        $lesson = $this->service->createFromDto($request->getDto());
        $lesson->load('instructor', 'course', 'controller');

        return new LessonResource($lesson);
    }

    /**
     * @param UpdateLessonRequest $request
     * @param string $id
     * @return LessonResource
     */
    public function update(UpdateLessonRequest $request, string $id): LessonResource
    {
        $lesson = $this->repository->find($id);
        $this->repository->update($lesson, $request->getDto());
        $lesson->load('instructor', 'course', 'controller');

        return new LessonResource($lesson);
    }

    /**
     * @param ChangeLessonInstructorRequest $request
     * @param string $id
     * @return LessonResource
     */
    public function changeInstructor(ChangeLessonInstructorRequest $request, string $id): LessonResource
    {
        $lesson = $this->repository->find($id);
        $this->repository->updateInstructor($lesson, $request->instructor_id);
        $lesson->load('instructor', 'course', 'controller');

        return new LessonResource($lesson);
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $lesson = $this->repository->find($id);
        $this->repository->delete($lesson);
    }

    /**
     * @param LessonsOnDateRequest $request
     * @return AnonymousResourceCollection
     */
    public function onDate(LessonsOnDateRequest $request): AnonymousResourceCollection
    {
        $lessons = $this->repository->getLessonsForDate($request->getDto());
        $lessons->load('instructor', 'course', 'controller');

        return LessonResource::collection($lessons);
    }

    /**
     * @param string $id
     * @return LessonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function close(string $id): LessonResource
    {
        $lesson = $this->repository->find($id);
        $this->service->close($lesson);

        return new LessonResource($lesson);
    }

    /**
     * @param string $id
     * @return LessonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function open(string $id): LessonResource
    {
        $lesson = $this->repository->find($id);
        $this->service->open($lesson);

        return new LessonResource($lesson);
    }

    /**
     * @param string $id
     * @return LessonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function cancel(string $id): LessonResource
    {
        $lesson = $this->repository->find($id);
        $this->service->cancel($lesson);

        return new LessonResource($lesson);
    }

    /**
     * @param string $id
     * @return LessonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function book(string $id): LessonResource
    {
        $lesson = $this->repository->find($id);
        $this->repository->book($lesson);

        return new LessonResource($lesson);
    }
}
