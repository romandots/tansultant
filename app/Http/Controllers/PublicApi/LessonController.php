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
use App\Http\Requests\PublicApi\LessonsOnDateRequest;
use App\Http\Resources\PublicApi\LessonResource;
use App\Services\Lesson\LessonFacade;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LessonController extends Controller
{
    private LessonFacade $lessons;

    public function __construct(LessonFacade $lessons)
    {
        $this->lessons = $lessons;
    }

    public function onDate(LessonsOnDateRequest $request): AnonymousResourceCollection
    {
        $lessons = $this->lessons->getLessonsOnDate($request->getDto());
        return LessonResource::collection($lessons);
    }
}
