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

/**
 * Class BranchController
 * @package App\Http\Controllers\PublicApi
 */
class BranchController extends Controller
{
    /**
     * @var BranchRepository
     */
    private $repository;

    /**
     * BranchController constructor.
     * @param BranchRepository $repository
     */
    public function __construct(BranchRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return AnonymousResourceCollection
     */
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
