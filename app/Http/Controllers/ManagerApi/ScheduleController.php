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
use App\Services\Schedule\ScheduleFacade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScheduleController extends Controller
{
    private ScheduleFacade $schedules;

    public function __construct(ScheduleFacade $scheduleFacade)
    {
        $this->schedules = $scheduleFacade;
    }

    public function index(string $courseId): AnonymousResourceCollection
    {
        $schedules = $this->schedules->getAllByCourseId($courseId);

        return ScheduleResource::collection($schedules);
    }

    public function store(StoreScheduleRequest $request): ScheduleResource
    {
        $schedule = $this->schedules->create($request->getDto());

        return new ScheduleResource($schedule);
    }

    public function update(StoreScheduleRequest $request, string $courseId, string $scheduleId): void
    {
        $this->schedules->findAndUpdate($scheduleId, $courseId, $request->getDto());
    }

    public function destroy(Request $request, string $courseId, string $scheduleId): void
    {
        $this->schedules->findAndDelete($scheduleId, $courseId, $request->user());
    }

    public function onDate(ScheduleOnDateRequest $request): AnonymousResourceCollection
    {
        $schedules = $this->schedules->getSchedulesForDateWithRelations(
            $request->getDto(),
            ['course.instructor.person']
        );

        return ScheduleResource::collection($schedules);
    }
}
