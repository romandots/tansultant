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
                "Класс {$ctx->old->class_title} ({$ctx->entity}#{$ctx->old->id}) удалён"
            );
        }

        if ($ctx->old->end_date !== null && strtotime($ctx->old->end_date) < time()) {
            throw new ImportSkippedException(
                "Класс {$ctx->old->class_title} ({$ctx->entity}#{$ctx->old->id}) уже не работает"
            );
        }

        return $next($ctx);
    }
}