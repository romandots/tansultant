<?php

namespace App\Services\Import\Pipes\Course;

use App\Models\Enum\Weekday;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use App\Services\Import\Traits\DatesTrait;
use App\Services\Import\Traits\PriceTrait;
use App\Services\Import\Traits\ScheduleTrait;
use Closure;

class CreateCourseSlots implements PipeInterface
{
    use DatesTrait, PriceTrait, ScheduleTrait;

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $branchId = $ctx->manager->ensureImported('branch', $ctx->old->studio_id, $ctx->level);
        $classroomId = $ctx->manager->ensureImported('classroom', $ctx->old->dancefloor_id, $ctx->level);
        $priceId = $this->getPriceId($ctx->old->price_rate, $ctx);

        $timeMap = [
            [Weekday::MONDAY, $ctx->old->mon],
            [Weekday::TUESDAY, $ctx->old->tue],
            [Weekday::WEDNESDAY, $ctx->old->wed],
            [Weekday::THURSDAY, $ctx->old->thu],
            [Weekday::FRIDAY, $ctx->old->fri],
            [Weekday::SATURDAY, $ctx->old->sat],
            [Weekday::SUNDAY, $ctx->old->sun],
        ];

        $slotsDtos = [];
        foreach ($timeMap as [$weekday, $time]) {
            if (!empty($time)) {
                $slotsDtos[] = $this->initScheduleDto(
                    courseId: $ctx->newId,
                    branchId: $branchId,
                    classroomId: $classroomId,
                    priceId: $priceId,
                    weekday: $weekday,
                    time: $time,
                    startDate: $ctx->old->start_date,
                    endDate: $ctx->old->end_date,
                    periods: $ctx->old->periods,
                    user: $ctx->adminUser,
                );
            }
        }

        $slotsIds = [];
        foreach ($slotsDtos as $slotDto) {
            try {
                $slotsIds[] = $this->getScheduleId($slotDto, $ctx);
            } catch (\Exception $e) {
                $ctx->error(
                    "Не удалось сохранить слот расписания для курса {$ctx->old->class_title}: {$e->getMessage()}"
                );
            }
        }

        $ctx->debug("Создали слоты расписания для курса {$ctx->old->class_title}: " . implode(", ", $slotsIds));

        return $next($ctx);
    }

}