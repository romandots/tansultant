<?php

namespace App\Services\Import\Pipes\Lesson;

use App\Components\Lesson\Dto;
use App\Components\Loader;
use App\Models\Course;
use App\Models\Enum\Weekday;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use App\Services\Import\Traits\PriceTrait;
use App\Services\Import\Traits\ScheduleTrait;
use Closure;

class ResolveLessonRelations implements \App\Services\Import\Contracts\PipeInterface
{
    use PriceTrait, ScheduleTrait;

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        /** @var Dto $dto */
        $dto = $ctx->dto;
        $dto->branch_id = $ctx->manager->ensureImported('branch', $ctx->old->studio_id, $ctx->level);
        $dto->price_id = $ctx->old->price ? $this->getPriceId($ctx->old->price, $ctx) : null;
        $dto->course_id = $ctx->manager->ensureImported('course', $ctx->old->class_id, $ctx->level);
        $dto->classroom_id = $ctx->manager->ensureImported('classroom', $ctx->old->dancefloor_id, $ctx->level);
        $dto->instructor_id = $ctx->manager->ensureImported('instructor', $ctx->old->teacher_id, $ctx->level);
        $dto->controller_id = $ctx->adminUser->id;

        try {
            /** @var Course $course */
            $course = Loader::courses()->findById($dto->course_id);
            $scheduleDto = $this->initScheduleDto(
                courseId: $dto->course_id,
                branchId: $dto->branch_id,
                classroomId: $dto->classroom_id,
                priceId: $dto->price_id,
                weekday: Weekday::from($dto->starts_at->dayOfWeek ?: 7),
                time: $ctx->old->time,
                startDate: $course->starts_at,
                endDate: $course->ends_at,
                periods: $ctx->old->periods,
                user: $ctx->adminUser,
            );
            $dto->schedule_id = $this->getScheduleId($scheduleDto, $ctx);
        } catch (\Throwable $e) {
            throw new ImportException("Не удалось получить слот расписания: {$e->getMessage()}");
        }

        return $next($ctx);
    }
}