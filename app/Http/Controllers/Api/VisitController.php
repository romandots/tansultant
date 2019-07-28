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
use App\Services\Visit\VisitService;

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
     * @var VisitService
     */
    private $service;

    /**
     * VisitController constructor.
     * @param VisitRepository $repository
     * @param VisitService $service
     */
    public function __construct(VisitRepository $repository, VisitService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @param StoreLessonVisitRequest $request
     * @return VisitResource
     * @throws \App\Services\Account\Exceptions\InsufficientFundsAccountServiceException
     */
    public function createLessonVisit(StoreLessonVisitRequest $request): VisitResource
    {
        $visit = $this->service->createLessonVisit($request->getDto(), $request->user());
        $visit->load('event', 'student', 'manager', 'payment');

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
        $visit->load('event', 'student', 'manager');

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
