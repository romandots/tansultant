<?php
/**
 * File: BranchController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\StoreBranchRequest;
use App\Http\Resources\ManagerApi\BranchResource;
use App\Repository\BranchRepository;

/**
 * Class BranchController
 * @package App\Http\Controllers\ManagerApi
 */
class BranchController extends Controller
{
    /**
     * @var BranchRepository
     */
    private $repository;

    /**
     * BranchController constructor.
     * @param BranchRepository $branchRepository
     */
    public function __construct(BranchRepository $branchRepository)
    {
        $this->repository = $branchRepository;
    }

    /**
     * @param string $id
     * @return BranchResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $id): BranchResource
    {
        $branch = $this->repository->find($id);

        return new BranchResource($branch);
    }

    /**
     * @param StoreBranchRequest $request
     * @return BranchResource
     * @throws \Exception
     */
    public function store(StoreBranchRequest $request): BranchResource
    {
        $branch = $this->repository->create($request->getDto());

        return new BranchResource($branch);
    }

    /**
     * @param StoreBranchRequest $request
     * @param string $id
     * @return BranchResource
     * @throws \Exception
     */
    public function update(StoreBranchRequest $request, string $id): BranchResource
    {
        $branch = $this->repository->find($id);
        $this->repository->update($branch, $request->getDto());

        return new BranchResource($branch);
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $branch = $this->repository->find($id);
        $this->repository->delete($branch);
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function recover(string $id): void
    {
        $branch = $this->repository->findWithDeleted($id);
        $this->repository->recover($branch);
    }
}
