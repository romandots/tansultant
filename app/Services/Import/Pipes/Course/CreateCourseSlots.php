<?php

namespace App\Services\Import\Pipes\Course;

use App\Components\Loader;
use App\Components\Schedule\Dto as ScheduleDto;
use App\Models\Enum\ScheduleCycle;
use App\Models\Enum\Weekday;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Closure;

class CreateCourseSlots implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $branchId = $ctx->manager->ensureImported('branch', $ctx->old->studio_id, $ctx->level);
        $classroomId = $ctx->manager->ensureImported('classroom', $ctx->old->dancefloor_id, $ctx->level);

        try {
            $price = Loader::prices()->findByPriceValue($ctx->old->price_rate);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            try {
                $priceDto = new \App\Components\Price\Dto();
                $priceDto->name = $ctx->old->price_rate;
                $priceDto->price = $ctx->old->price_rate;
                $price = Loader::prices()->create($priceDto);
                $ctx->manager->increaseCounter('price');
            } catch (\Exception $e) {
                throw new ImportException("Ошибка создания цены: {$e->getMessage()}");
            }
        }

        $slotsDtos = [];
        if ($ctx->old->mon) {
            $slotsDtos[] = $this->initScheduleDto(
                $ctx->newId,
                $branchId,
                $classroomId,
                $price->id,
                Weekday::MONDAY,
                $ctx->old->mon,
                $ctx->old
            );
        }

        if ($ctx->old->tue) {
            $slotsDtos[] = $this->initScheduleDto(
                $ctx->newId,
                $branchId,
                $classroomId,
                $price->id,
                Weekday::TUESDAY,
                $ctx->old->tue,
                $ctx->old
            );
        }

        if ($ctx->old->wed) {
            $slotsDtos[] = $this->initScheduleDto(
                $ctx->newId,
                $branchId,
                $classroomId,
                $price->id,
                Weekday::WEDNESDAY,
                $ctx->old->wed,
                $ctx->old
            );
        }

        if ($ctx->old->thu) {
            $slotsDtos[] = $this->initScheduleDto(
                $ctx->newId,
                $branchId,
                $classroomId,
                $price->id,
                Weekday::THURSDAY,
                $ctx->old->thu,
                $ctx->old
            );
        }

        if ($ctx->old->fri) {
            $slotsDtos[] = $this->initScheduleDto(
                $ctx->newId,
                $branchId,
                $classroomId,
                $price->id,
                Weekday::FRIDAY,
                $ctx->old->fri,
                $ctx->old
            );
        }

        if ($ctx->old->sat) {
            $slotsDtos[] = $this->initScheduleDto(
                $ctx->newId,
                $branchId,
                $classroomId,
                $price->id,
                Weekday::SATURDAY,
                $ctx->old->sat,
                $ctx->old
            );
        }

        if ($ctx->old->sun) {
            $slotsDtos[] = $this->initScheduleDto(
                $ctx->newId,
                $branchId,
                $classroomId,
                $price->id,
                Weekday::SUNDAY,
                $ctx->old->sun,
                $ctx->old
            );
        }

        $slotsIds = [];
        foreach ($slotsDtos as $slotsDto) {
            try {
                $slotsIds[] = Loader::schedules()->create($slotsDto)?->id;
                $ctx->manager->increaseCounter('schedule');
            } catch (\Exception $e) {
                $ctx->error(
                    "Не удалось сохранить слот расписания для курса {$ctx->old->class_title}: {$e->getMessage()}"
                );
            }
        }

        $ctx->debug("Создали слоты расписания для курса {$ctx->old->class_title}: " . implode(", ", $slotsIds));

        return $next($ctx);
    }

    protected function initScheduleDto(
        string $courseId,
        string $branchId,
        string $classroomId,
        string $priceId,
        Weekday $weekday,
        string $time,
        object $old
    ): ScheduleDto {
        $scheduleDto = new ScheduleDto();
        $scheduleDto->course_id = $courseId;
        $scheduleDto->branch_id = $branchId;
        $scheduleDto->classroom_id = $classroomId;
        $scheduleDto->price_id = $priceId;
        $scheduleDto->cycle = ScheduleCycle::EVERY_WEEK;
        $scheduleDto->weekdays = [$weekday];

        try {
            $scheduleDto->starts_at = \Carbon\Carbon::createFromFormat('H:i:s', $time);
            $scheduleDto->ends_at = \Carbon\Carbon::createFromFormat('H:i:s', $time)->addHours($old->periods / 2);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            throw new ImportException("Неверный формат времени: {$time}");
        }

        try {
            $scheduleDto->from_date = $old->start_date ? \Carbon\Carbon::createFromFormat(
                'Y-m-d',
                $old->start_date
            ) : null;
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            throw new ImportException("Неверный формат даты начала: {$old->start_date}");
        }

        try {
            $scheduleDto->to_date = $old->end_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $old->end_date) : null;
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            throw new ImportException("Неверный формат даты окончания: {$old->end_date}");
        }

        return $scheduleDto;
    }
}