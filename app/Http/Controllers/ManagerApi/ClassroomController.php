<?php
/**
 * File: ClassroomController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Common\Requests\SuggestRequest;
use App\Components\Classroom as Component;
use App\Http\Requests\ManagerApi\SearchClassroomsRequest;
use App\Http\Requests\ManagerApi\StoreClassroomRequest;
use App\Models\Classroom;

/**
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection index()
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection _search(\App\Common\Requests\SearchRequest $request)
 * @method Component\Formatter show(string $id)
 * @method Component\Formatter _store(\App\Common\Requests\StoreRequest $request)
 * @method Component\Formatter _update(string $id, \App\Common\Requests\StoreRequest $request)
 * @method void destroy(string $id, \Illuminate\Http\Request $request)
 * @method void restore(string $id, \Illuminate\Http\Request $request)
 * @method Component\Facade getFacade()
 * @method \Illuminate\Http\Resources\Json\JsonResource makeResource(\App\Models\Contract $record)
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection makeResourceCollection(\Illuminate\Support\Collection $collection)
 */
class ClassroomController extends AdminController
{
    public function __construct()
    {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: ['branch'],
            singleRecordRelations: ['branch'],
        );
    }

    public function search(SearchClassroomsRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_search($request);
    }

    public function store(StoreClassroomRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function update(string $id, StoreClassroomRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_update($id, $request);
    }

    public function suggest(SuggestRequest $request): array
    {
        $labelField = fn(Classroom $classroom) => sprintf('%s (%s)', $classroom->name, $classroom->branch->name);
        $extraFields = [
            'branch' => function (Classroom $classroom) {
                return $classroom->branch->name;
            },
            'branch_id' => 'branch_id',
        ];

        return $this->_suggest($request, $labelField, 'id', $extraFields);
    }
}
