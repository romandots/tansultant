<?php
/**
 * File: LessonController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Components\Lesson as Component;
use App\Http\Requests\ManagerApi\SearchLessonsRequest;
use App\Http\Requests\ManagerApi\StoreLessonRequest;

/**
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection index()
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection _search(\App\Common\Requests\SearchRequest $request)
 * @method array suggest(\App\Common\Requests\SuggestRequest $request)
 * @method Component\Formatter show(string $id)
 * @method Component\Formatter _store(\App\Common\Requests\StoreRequest $request)
 * @method Component\Formatter _update(string $id, \App\Common\Requests\StoreRequest $request)
 * @method void destroy(string $id, \Illuminate\Http\Request $request)
 * @method void restore(string $id, \Illuminate\Http\Request $request)
 * @method Component\Facade getFacade()
 * @method Component\Formatter makeResource(\App\Models\Contract $record)
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection makeResourceCollection(\Illuminate\Support\Collection $collection)
 */
class LessonController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: ['schedule', 'course', 'instructor'],
            singleRecordRelations: ['schedule', 'course', 'instructor'],
        );
    }

    public function search(SearchLessonsRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function store(StoreLessonRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function update(string $id, StoreLessonRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_update($id, $request);
    }

    public function close(string $id): Component\Formatter
    {
        $lesson = $this->getFacade()->findAndClose($id);
        return $this->makeResource($lesson);
    }

    public function open(string $id): Component\Formatter
    {
        $lesson = $this->getFacade()->findAndOpen($id);
        return $this->makeResource($lesson);
    }

    public function cancel(string $id): Component\Formatter
    {
        $lesson = $this->getFacade()->findAndCancel($id);
        return $this->makeResource($lesson);
    }

    public function book(string $id): Component\Formatter
    {
        $lesson = $this->getFacade()->findAndBook($id);
        return $this->makeResource($lesson);
    }
}
