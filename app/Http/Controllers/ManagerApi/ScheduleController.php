<?php
/**
 * File: ScheduleController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\StoreScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Repository\ScheduleRepository;
use App\Services\Schedule\ScheduleService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ScheduleController
 * @package App\Http\Controllers
 */
class ScheduleController extends Controller
{
    /**
     * @var ScheduleRepository
     */
    private $repository;

    /**
     * ScheduleController constructor.
     * @param ScheduleRepository $repository
     * @param ScheduleService $service
     */
    public function __construct(ScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $schedules = $this->repository->getAll();

        return ScheduleResource::collection($schedules);
    }

    /**
     * @param StoreScheduleRequest $request
     * @return ScheduleResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function store(StoreScheduleRequest $request): ScheduleResource
    {
        $schedule = $this->repository->create($request->getDto());
        $schedule->load('course');

        return new ScheduleResource($schedule);
    }

    /**
     * @param string $id
     * @return ScheduleResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $id): ScheduleResource
    {
        $schedule = $this->repository->find($id);
        $schedule->load('course');

        return new ScheduleResource($schedule);
    }

    /**
     * @param StoreScheduleRequest $request
     * @param string $id
     * @return ScheduleResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(StoreScheduleRequest $request, string $id): ScheduleResource
    {
        $schedule = $this->repository->find($id);
        $this->repository->update($schedule, $request->getDto());
        $schedule->load('course');

        return new ScheduleResource($schedule);
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $schedule = $this->repository->find($id);
        $this->repository->delete($schedule);
    }
}
