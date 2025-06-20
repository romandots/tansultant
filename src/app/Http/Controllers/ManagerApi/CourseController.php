<?php
/**
 * File: CourseController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Common\Requests\ManageRelationsRequest;
use App\Components\Course as Component;
use App\Http\Requests\ManagerApi\SearchCourseRequest;
use App\Http\Requests\ManagerApi\StoreCourseRequest;
use Illuminate\Http\Request;

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
 * @method \Illuminate\Http\Resources\Json\JsonResource makeResource(\App\Models\Contract $record)
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection makeResourceCollection(\Illuminate\Support\Collection $collection)
 */
class CourseController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: ['schedules', 'instructor.person'],
            singleRecordRelations: ['schedules', 'instructor.person'],
        );
    }

    public function search(SearchCourseRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function store(StoreCourseRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function update(string $id, StoreCourseRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_update($id, $request);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return void
     * @throws \Exception
     */
    public function disable(Request $request, string $id): void
    {
        $this->getFacade()->disable($id, $request->user());
    }

    /**
     * @param Request $request
     * @param string $id
     * @return void
     * @throws \Exception
     */
    public function enable(Request $request, string $id): void
    {
        $this->getFacade()->enable($id, $request->user());
    }

    public function attachTariffs(ManageRelationsRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        $course = $this->getFacade()->findAndAttachTariffs($request->getDto());
        return new \App\Components\Course\Formatter($course);
    }

    public function detachTariffs(ManageRelationsRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        $course = $this->getFacade()->findAndDetachTariffs($request->getDto());
        return new \App\Components\Course\Formatter($course);
    }
}
