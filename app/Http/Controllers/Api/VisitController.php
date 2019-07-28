<?php
/**
 * File: VisitController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreLessonVisitRequest;
use App\Http\Resources\VisitResource;
use App\Repository\VisitRepository;

/**
 * Class VisitController
 * @package App\Http\Controllers\Api
 */
class VisitController extends Controller
{
    /**
     * @var VisitRepository
     */
    private $repository;

    /**
     * VisitController constructor.
     * @param VisitRepository $repository
     */
    public function __construct(VisitRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param StoreLessonVisitRequest $request
     * @return VisitResource
     */
    public function store(StoreLessonVisitRequest $request): VisitResource
    {
        $visit = $this->repository->createFromDto($request->getDto(), $request->user());

        return new VisitResource($visit);
    }

    /**
     * @param int $id
     * @return VisitResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(int $id): VisitResource
    {
        $visit = $this->repository->find($id);

        return new VisitResource($visit);
    }

    /**
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(int $id): void
    {
        $visit = $this->repository->find($id);
        $this->repository->delete($visit);
    }
}
