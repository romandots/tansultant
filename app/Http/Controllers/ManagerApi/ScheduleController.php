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
use App\Http\Requests\ManagerApi\ScheduleOnDateRequest;
use App\Http\Requests\ManagerApi\StoreScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Repository\ScheduleRepository;
use App\Services\Schedule\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScheduleController extends Controller
{
    private ScheduleRepository $repository;
    private ScheduleService $service;

    public function __construct(ScheduleRepository $repository, ScheduleService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(string $courseId): AnonymousResourceCollection
    {
        $schedules = $this->repository->getAllByCourseId($courseId);

        return ScheduleResource::collection($schedules);
    }

    /**
     * @param StoreScheduleRequest $request
     * @return ScheduleResource
     * @throws \App\Repository\Exceptions\ScheduleSlotIsOccupied
     * @throws \Exception
     */
    public function store(StoreScheduleRequest $request): ScheduleResource
    {
        $schedule = $this->service->create($request->getDto());

        return new ScheduleResource($schedule);
    }

    /**
     * @param StoreScheduleRequest $request
     * @param string $courseId
     * @param string $scheduleId
     * @throws \App\Repository\Exceptions\ScheduleSlotIsOccupied
     * @throws \Exception
     */
    public function update(StoreScheduleRequest $request, string $courseId, string $scheduleId): void
    {
        $schedule = $this->repository->findByIdAndCourseId($scheduleId, $courseId);
        $this->service->update($schedule, $request->getDto());
    }

    /**
     * @param Request $request
     * @param string $courseId
     * @param string $scheduleId
     * @throws \Exception
     */
    public function destroy(Request $request, string $courseId, string $scheduleId): void
    {
        $schedule = $this->repository->findByIdAndCourseId($scheduleId, $courseId);
        $this->service->delete($schedule, $request->user());
    }

    public function onDate(ScheduleOnDateRequest $request): AnonymousResourceCollection
    {
        $schedules = $this->repository->getSchedulesForDateWithRelations($request->getDto(),
            ['course.instructor.person']);

        return ScheduleResource::collection($schedules);
    }
}
