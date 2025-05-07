<?php

namespace App\Services\Import\Pipes\Course;

use App\Components\Course\Dto;
use App\Models\Enum\CourseStatus;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class MapCourseEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $ctx->dto = new Dto();
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

        try {
            $ctx->dto->starts_at = \Carbon\Carbon::createFromFormat('Y-m-d', $ctx->old->start_date);
        } catch (\Exception $e) {
            $ctx->dto->starts_at = null;
        }
        try {
            $ctx->dto->ends_at = \Carbon\Carbon::createFromFormat('Y-m-d', $ctx->old->end_date);
        } catch (\Exception $e) {
            $ctx->dto->ends_at = null;
        }

        $ctx->dto->display = !$ctx->old->hidden;
        $ctx->dto->status = CourseStatus::ACTIVE;

        return $next($ctx);
    }
}