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
use App\Http\Requests\ManagerApi\SuggestRequest;
use App\Http\Resources\ManagerApi\ClassroomResource;
use App\Models\Classroom;
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
    private ClassroomFacade $facade;

    public function __construct(ClassroomFacade $facade)
    {
        $this->facade = $facade;
    }

    public function getFacade(): BaseFacade
    {
        return $this->facade;
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
        $record = $this->facade->create($request->getDto());
        return $this->makeResource($record);
    }

    public function update(UpdateClassroomRequest $request, string $id): JsonResource
    {
        $record = $this->facade->findAndUpdate($id, $request->getDto());
        return $this->makeResource($record);
    }

    public function suggest(SuggestRequest $request): array
    {
        $extraFields = [
            'branch' => function (Classroom $classroom) {
                return $classroom->branch->name;
            },
            'branch_id' => 'branch_id',
        ];
        return $this->facade->suggest(
            $request->getQuery(),
            function(Classroom $classroom) {
                return sprintf('%s (%s)', $classroom->name, $classroom->branch->name);
            },
            'id',
            $extraFields
        );
    }
}
