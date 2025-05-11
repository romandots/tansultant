<?php

namespace App\Services\Import\Pipes\Tariff;

use App\Common\DTO\IdsDto;
use App\Components\Loader;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\Exceptions\ImportSkippedException;
use App\Services\Import\ImportContext;
use Closure;

class AttachCourses implements \App\Services\Import\Contracts\PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        try {
            $classes = $ctx->old->default_classes_included
                ? array_filter(unserialize($ctx->old->default_classes_included, ['allowed_classes' => false]))
                : [];
        } catch (\Throwable $e) {
            $classes = [];
        }
        if (empty($classes)) {
            return $next($ctx);
        }

        $coursesIds = new IdsDto();
        $coursesIds->id = $ctx->newId;
        foreach ($classes as $classId) {
            try {
                $coursesIds->relations_ids[] = $ctx->manager->ensureImported('course', $classId);
            } catch (ImportSkippedException|ImportException) {
                continue;
            }
        }

        try {
            Loader::tariffs()->findAndAttachCourses($coursesIds);
        } catch (\Throwable $throwable) {
            throw new \App\Services\Import\Exceptions\ImportException(
                "Ошибка привязки курсов к тарифу ({$ctx->old?->ticket_type_name}): {$throwable->getMessage()}",
                $ctx->toArray()
            );
        }

        return $next($ctx);
    }
}