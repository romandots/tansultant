<?php

namespace App\Services\Import\Pipes\Course;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportSkippedException;
use App\Services\Import\ImportContext;
use Closure;

class SkipDeletedAndInactiveCourse implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        if ($ctx->old->deleted) {
            throw new ImportSkippedException(
                "Класс {$ctx->old->class_title} удалён"
            );
        }

        if ($ctx->old->end_date !== null && strtotime($ctx->old->end_date) < time()) {
            throw new ImportSkippedException(
                "Класс {$ctx->old->class_title} уже не работает"
            );
        }

        if (empty($ctx->old->teacher_id)) {
            throw new ImportSkippedException(
                "Класс {$ctx->old->class_title} не имеет преподавателя"
            );
        }

        return $next($ctx);
    }
}