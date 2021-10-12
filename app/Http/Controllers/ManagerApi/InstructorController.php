<?php
/**
 * File: InstructorController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\AdminController;
use App\Http\Requests\ManagerApi\StoreInstructorRequest;
use App\Http\Requests\ManagerApi\UpdateInstructorRequest;
use App\Http\Resources\InstructorResource;
use App\Services\BaseFacade;
use App\Services\Instructor\InstructorFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class InstructorController extends AdminController
{
    protected InstructorFacade $facade;

    public function __construct(InstructorFacade $facade)
    {
        $this->facade = $facade;
    }

    public function getFacade(): BaseFacade
    {
        return $this->facade;
    }

    public function makeResource(Model $record): JsonResource
    {
        return new InstructorResource($record);
    }

    public function makeResourceCollection(Collection $collection): AnonymousResourceCollection
    {
        return InstructorResource::collection($collection);
    }

    protected function getSearchRelations(): array
    {
        return ['person'];
    }

    protected function getSingleRecordRelations(): array
    {
        return ['person'];
    }

    public function store(StoreInstructorRequest $request): InstructorResource
    {
        $instructor = $this->facade->create($request->getDto());
        return new InstructorResource($instructor);
    }

    public function update(string $id, UpdateInstructorRequest $request): InstructorResource
    {
        $instructor = $this->facade->findAndUpdate($id, $request->getDto());
        return new InstructorResource($instructor);
    }
}
