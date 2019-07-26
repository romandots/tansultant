<?php
/**
 * File: LessonController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LessonsOnDateRequest;
use App\Http\Requests\Api\StoreLessonRequest;
use App\Http\Requests\Api\StoreLessonRequest as UpdateLessonRequest;
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
     * @param int $id
     * @return LessonResource
     */
    public function show(int $id): LessonResource
    {
        $lesson = $this->repository->find($id);
        $lesson->load('instructor', 'course', 'controller');

        return new LessonResource($lesson);
    }

    /**
     * @param StoreLessonRequest $request
     * @return LessonResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function store(StoreLessonRequest $request): LessonResource
    {
        $lesson = $this->service->create($request->getDto());
        $lesson->load('instructor', 'course', 'controller');

        return new LessonResource($lesson);
    }

    /**
     * @param UpdateLessonRequest $request
     * @param int $id
     * @return LessonResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(UpdateLessonRequest $request, int $id): LessonResource
    {
        $lesson = $this->repository->find($id);
        $this->repository->update($lesson, $request->getDto());
        $lesson->load('instructor', 'course', 'controller');

        return new LessonResource($lesson);
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function destroy(int $id): void
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
}
