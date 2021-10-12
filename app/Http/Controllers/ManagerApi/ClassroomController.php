<?php
/**
 * File: ClassroomController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\AdminController;
use App\Http\Requests\ManagerApi\StoreClassroomRequest;
use App\Http\Requests\ManagerApi\StoreClassroomRequest as UpdateClassroomRequest;
use App\Http\Resources\ManagerApi\ClassroomResource;
use App\Repository\ClassroomRepository;
use App\Services\BaseFacade;
use App\Services\Classroom\ClassroomFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;

class ClassroomController extends AdminController
{
    private ClassroomFacade $classrooms;

    public function __construct(ClassroomRepository $repository, ClassroomFacade $classrooms)
    {
        $this->classrooms = $classrooms;
    }

    public function getFacade(): BaseFacade
    {
        return $this->classrooms;
    }

    #[Pure] public function makeResource(Model $record): JsonResource
    {
        return new ClassroomResource($record);
    }

    public function makeResourceCollection(Collection $collection): AnonymousResourceCollection
    {
        return ClassroomResource::collection($collection);
    }

    public function store(StoreClassroomRequest $request): JsonResource
    {
        return $this->_store($request);
    }

    public function update(UpdateClassroomRequest $request, string $id): JsonResource
    {
        return $this->_update($request, $id);
    }
}
