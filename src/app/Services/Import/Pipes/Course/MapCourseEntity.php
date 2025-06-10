<?php

namespace App\Services\Import\Pipes\Course;

use App\Components\Course\Dto;
use App\Models\Enum\CourseStatus;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use App\Services\Import\Traits\DatesTrait;
use Closure;

class MapCourseEntity implements PipeInterface
{
    use DatesTrait;

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $ctx->dto = new Dto($ctx->adminUser);
        $ctx->dto->name = $ctx->old->class_title;
        $ctx->dto->description = $ctx->old->description;
        $ctx->dto->summary = $ctx->old->description;

        $ageRestrictions = str_replace(' ', '', $ctx->old->age_restrictions);
        if (!empty($ageRestrictions)) {
            if (preg_match('/^(\d+)-(\d+)$/', $ageRestrictions, $matches)) {
                $ctx->dto->age_restrictions = [
                    'from' => (int)$matches[1],
                    'to' => (int)$matches[2],
                ];
            } elseif (preg_match('/^(\d+)\+$/', $ageRestrictions, $matches)) {
                $ctx->dto->age_restrictions = [
                    'from' => (int)$matches[1],
                    'to' => null,
                ];
            }
        }
        $ctx->dto->starts_at = $ctx->old->start_date
            ? $this->getCarbonObjectFromFormattedDate(
                $ctx->old->start_date,
                'Y-m-d',
                "Неверный формат даты начала: {$ctx->old->start_date}"
            )
            : null;
        $ctx->dto->ends_at = $ctx->old->end_date ? $this->getCarbonObjectFromFormattedDate(
            $ctx->old->end_date,
            'Y-m-d',
            "Неверный формат даты окончания: {$ctx->old->end_date}"
        ) : null;

        $ctx->dto->display = !$ctx->old->hidden;
        $ctx->dto->status = CourseStatus::ACTIVE;

        return $next($ctx);
    }
}