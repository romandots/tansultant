<?php
/**
 * File: ClassroomController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\StoreClassroomRequest;
use App\Http\Requests\ManagerApi\StoreClassroomRequest as UpdateClassroomRequest;
use App\Http\Resources\ManagerApi\ClassroomResource;
use App\Repository\ClassroomRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClassroomController extends Controller
{
    private ClassroomRepository $repository;

    public function __construct(ClassroomRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(string $branchId): AnonymousResourceCollection
    {
        $records = $this->repository->getAll();

        return ClassroomResource::collection($records);
    }

    /**
     * @param UpdateClassroomRequest $request
     * @param string $branchId
     * @return ClassroomResource
     * @throws \Exception
     */
    public function store(StoreClassroomRequest $request, string $branchId): ClassroomResource
    {
        $record = $this->repository->create($request->getDto());

        return new ClassroomResource($record);
    }

    /**
     * @param string $branchId
     * @param string $classroomId
     * @return ClassroomResource
     */
    public function show(string $branchId, string $classroomId): ClassroomResource
    {
        $record = $this->repository->find($classroomId);

        return new ClassroomResource($record);
    }

    /**
     * @param UpdateClassroomRequest $request
     * @param string $branchId
     * @param string $id
     * @return ClassroomResource
     */
    public function update(UpdateClassroomRequest $request, string $branchId, string $id): ClassroomResource
    {
        $record = $this->repository->find($id);
        $this->repository->update($record, $request->getDto());

        return new ClassroomResource($record);
    }

    /**
     * @param string $branchId
     * @param string $classroomId
     * @throws \Exception
     */
    public function destroy(string $branchId, string $classroomId): void
    {
        $record = $this->repository->find($classroomId);
        $this->repository->delete($record);
    }

    /**
     * @param string $branchId
     * @param string $classroomId
     */
    public function restore(string $branchId, string $classroomId): void
    {
        $record = $this->repository->findWithDeleted($classroomId);
        $this->repository->restore($record);
    }
}
