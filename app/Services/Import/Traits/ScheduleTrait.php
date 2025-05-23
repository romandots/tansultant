<?php

namespace App\Services\Import\Traits;

use App\Components\Loader;
use App\Components\Schedule\Dto as ScheduleDto;
use App\Models\Enum\ScheduleCycle;
use App\Models\Enum\Weekday;
use App\Models\User;
use App\Services\Import\ImportContext;
use Carbon\Carbon;

trait ScheduleTrait
{
    use DatesTrait;

    protected function getScheduleId(ScheduleDto $scheduleDto, ImportContext $ctx): string
    {
        try {
            return Loader::schedules()->findByDto($scheduleDto)?->id;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $id = Loader::schedules()->create($scheduleDto)?->id;
            $ctx->manager->increaseCounter('schedule');
            return $id;
        }
    }

    protected function initScheduleDto(
        string $courseId,
        string $branchId,
        string $classroomId,
        ?string $priceId,
        Weekday $weekday,
        string $time,
        Carbon|string|null $startDate,
        Carbon|string|null $endDate,
        int $periods,
        User $user,
    ): ScheduleDto {
        $scheduleDto = new ScheduleDto($user);
        $scheduleDto->course_id = $courseId;
        $scheduleDto->branch_id = $branchId;
        $scheduleDto->classroom_id = $classroomId;
        $scheduleDto->price_id = $priceId;
        $scheduleDto->cycle = ScheduleCycle::EVERY_WEEK;
        $scheduleDto->weekdays = [$weekday];
        $scheduleDto->weekday = $weekday;
        $scheduleDto->starts_at = $this->getCarbonObjectFromFormattedDate(
            $time,
            'H:i:s',
            "Не распознано время начала: {$time}"
        );
        $scheduleDto->ends_at = $scheduleDto->starts_at->clone()->addHours($periods / 2);

        if ($startDate) {
            $scheduleDto->from_date = $startDate instanceof Carbon
                ? $startDate
                : $this->getCarbonObjectFromFormattedDate(
                    $startDate,
                    'Y-m-d',
                    "Неверный формат даты начала: {$startDate}"
                );
        }

        if ($endDate) {
            $scheduleDto->to_date = $endDate instanceof Carbon
                ? $endDate
                : $this->getCarbonObjectFromFormattedDate(
                    $endDate,
                    'Y-m-d',
                    "Неверный формат даты окончания: {$endDate}"
                );
        }

        return $scheduleDto;
    }

}