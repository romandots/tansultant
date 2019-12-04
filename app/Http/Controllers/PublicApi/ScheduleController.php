<?php
/**
 * File: ScheduleController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicApi\ScheduleOnDateRequest;
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
     */
    public function __construct(ScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ScheduleOnDateRequest $request
     * @return AnonymousResourceCollection
     */
    public function onDate(ScheduleOnDateRequest $request): AnonymousResourceCollection
    {
        $schedules = $this->repository->getSchedulesForDateWithRelations($request->getDto(), ['course.instructor.person']);

        return ScheduleResource::collection($schedules);
    }
}
