<?php
/**
 * File: ClassroomController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreClassroomRequest;
use App\Http\Requests\Api\StoreClassroomRequest as UpdateClassroomRequest;
use App\Http\Resources\Api\ClassroomResource;
use App\Repository\ClassroomRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ClassroomController
 * @package App\Http\Controllers\Api
 */
class ClassroomController extends Controller
{
    /**
     * @var ClassroomRepository
     */
    private $repository;

    /**
     * ClassroomController constructor.
     * @param ClassroomRepository $repository
     */
    public function __construct(ClassroomRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $records = $this->repository->getAll();

        return ClassroomResource::collection($records);
    }

    /**
     * @param UpdateClassroomRequest $request
     * @return ClassroomResource
     */
    public function store(StoreClassroomRequest $request): ClassroomResource
    {
        $record = $this->repository->create($request->getDto());

        return new ClassroomResource($record);
    }

    /**
     * @param int $id
     * @return ClassroomResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(int $id): ClassroomResource
    {
        $record = $this->repository->find($id);

        return new ClassroomResource($record);
    }

    /**
     * @param UpdateClassroomRequest $request
     * @param int $id
     * @return ClassroomResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(UpdateClassroomRequest $request, int $id): ClassroomResource
    {
        $record = $this->repository->find($id);
        $this->repository->update($record, $request->getDto());

        return new ClassroomResource($record);
    }

    /**
     * @param int $id
     * @return ClassroomRepository
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(int $id): ClassroomRepository
    {
        $record = $this->repository->find($id);
        $this->repository->delete($record);
    }
}
