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
use App\Http\Requests\ManagerApi\LessonsFilteredRequest;
use App\Http\Requests\ManagerApi\SearchLessonsRequest;
use App\Http\Requests\ManagerApi\StoreLessonRequest;
use App\Http\Requests\ManagerApi\StoreLessonRequest as UpdateLessonRequest;
use App\Http\Resources\ManagerApi\LessonResource;
use App\Services\Lesson\LessonFacade;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LessonController
{
    protected LessonFacade $lessons;

    public function __construct(LessonFacade $lessons)
    {
        $this->lessons = $lessons;
    }

    public function index(SearchLessonsRequest $request): AnonymousResourceCollection
    {

        $searchLessons = $request->getDto();
        $lessons = $this->lessons->search($searchLessons, []);
        $meta = $this->lessons->getMeta($searchLessons);

        return LessonResource::collection($lessons)->additional(['meta' => $meta]);
    }

    public function show(string $id): LessonResource
    {
        $lesson = $this->lessons->find($id);

        return new LessonResource($lesson);
    }

    /**
     * @param StoreLessonRequest $request
     * @return LessonResource
     * @throws \Exception
     */
    public function store(StoreLessonRequest $request): LessonResource
    {
        $lesson = $this->lessons->createFromDto($request->getDto());
        return new LessonResource($lesson);
    }

    public function update(UpdateLessonRequest $request, string $id): LessonResource
    {
        $lesson = $this->lessons->findAndUpdate($id, $request->getDto());
        return new LessonResource($lesson);
    }

    public function changeInstructor(ChangeLessonInstructorRequest $request, string $id): LessonResource
    {
        $lesson = $this->lessons->findAndChangeInstructor($id, $request->instructor_id);
        return new LessonResource($lesson);
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $this->lessons->findAndDelete($id);
    }

    public function search(LessonsFilteredRequest $request): AnonymousResourceCollection
    {
        $lessons = $this->lessons->getLessonsFiltered($request->getDto());
        return LessonResource::collection($lessons);
    }

    /**
     * @param string $id
     * @return LessonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function close(string $id): LessonResource
    {
        $lesson = $this->lessons->findAndClose($id);
        return new LessonResource($lesson);
    }

    /**
     * @param string $id
     * @return LessonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function open(string $id): LessonResource
    {
        $lesson = $this->lessons->findAndOpen($id);
        return new LessonResource($lesson);
    }

    /**
     * @param string $id
     * @return LessonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function cancel(string $id): LessonResource
    {
        $lesson = $this->lessons->findAndCancel($id);
        return new LessonResource($lesson);
    }

    /**
     * @param string $id
     * @return LessonResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function book(string $id): LessonResource
    {
        $lesson = $this->lessons->findAndBook($id);
        return new LessonResource($lesson);
    }
}
