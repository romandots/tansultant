<?php

namespace App\Services\Import\Pipes\Lesson;

use App\Models\Enum\LessonStatus;
use App\Models\Enum\LessonType;
use App\Services\Import\ImportContext;
use App\Services\Import\Traits\DatesTrait;
use Closure;

class MapLessonEntity implements \App\Services\Import\Contracts\PipeInterface
{
    use DatesTrait;

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $ctx->dto = new \App\Components\Lesson\Dto($ctx->adminUser);
        $ctx->dto->name = $ctx->old->lesson_title;
        $ctx->dto->type = LessonType::LESSON;

        $ctx->dto->starts_at = $this->getCarbonObjectFromTimestamp(
            $this->getLessonStartTimestamp($ctx->old),
            "Не распознано время начала урока: {$ctx->old->date} {$ctx->old->time}"
        );
        $ctx->dto->ends_at = $this->getCarbonObjectFromTimestamp(
            $this->getLessonEndTimestamp($ctx->old),
            "Не распознано время окончания урока: {$ctx->old->date} {$ctx->old->time} + {$ctx->old->periods} periods"
        );

        $ctx->dto->status = match (true) {
            !$ctx->old->booked => LessonStatus::CANCELED,
            $ctx->old->booked && $ctx->dto->starts_at->isFuture() => LessonStatus::BOOKED,
            $ctx->old->booked && !$ctx->dto->starts_at->isFuture() && $ctx->dto->ends_at->isFuture() => LessonStatus::ONGOING,
            $ctx->old->booked && empty($ctx->old->balance) && !$ctx->dto->ends_at->isFuture() => LessonStatus::PASSED,
            $ctx->old->booked && !empty($ctx->old->balance) && !$ctx->dto->ends_at->isFuture() => LessonStatus::CHECKED_OUT,
            default => LessonStatus::CLOSED,
        };

        $ctx->dto->controller_id = $ctx->adminUser->id;

        return $next($ctx);
    }
}