<?php
/**
 * File: ClassroomController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicApi\ClassroomResource;
use App\Repository\ClassroomRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ClassroomController
 * @package App\Http\Controllers\PublicApi
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
     * @param FilterClassroomRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterClassroomRequest $request): AnonymousResourceCollection
    {
        $filterClassroom = $request->getDto();
        $records = $filterClassroom->branch_id
            ? $this->repository->getByBranchId($filterClassroom->branch_id)
            : $this->repository->getAll();

        return ClassroomResource::collection($records);
    }

    /**
     * @param int $id
     * @return ClassroomResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(int $id): ClassroomResource
    {
        $record = $this->repository->find($id);
        $record->load('branch');

        return new ClassroomResource($record);
    }
}
