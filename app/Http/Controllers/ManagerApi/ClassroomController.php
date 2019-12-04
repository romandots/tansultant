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
     * @throws \Exception
     */
    public function store(StoreClassroomRequest $request): ClassroomResource
    {
        $record = $this->repository->create($request->getDto());

        return new ClassroomResource($record);
    }

    /**
     * @param string $id
     * @return ClassroomResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $id): ClassroomResource
    {
        $record = $this->repository->find($id);

        return new ClassroomResource($record);
    }

    /**
     * @param UpdateClassroomRequest $request
     * @param string $id
     * @return ClassroomResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(UpdateClassroomRequest $request, string $id): ClassroomResource
    {
        $record = $this->repository->find($id);
        $this->repository->update($record, $request->getDto());

        return new ClassroomResource($record);
    }

    /**
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $record = $this->repository->find($id);
        $this->repository->delete($record);
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function restore(string $id): void
    {
        $record = $this->repository->findWithDeleted($id);
        $this->repository->restore($record);
    }
}
