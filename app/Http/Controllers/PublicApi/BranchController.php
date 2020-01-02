<?php
/**
 * File: BranchController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicApi\BranchResource;
use App\Repository\BranchRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BranchController extends Controller
{
    private BranchRepository $repository;

    public function __construct(BranchRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): AnonymousResourceCollection
    {
        $records = $this->repository->getAll();

        return BranchResource::collection($records);
    }

    /**
     * @param string $id
     * @return BranchResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $id): BranchResource
    {
        $record = $this->repository->find($id);

        return new BranchResource($record);
    }
}
