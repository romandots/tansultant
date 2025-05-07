<?php

namespace App\Services\Import\Pipes\Course;

use App\Components\Course\Dto;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class ResolveCourseRelations implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        /** @var Dto $dto */
        $dto = $ctx->dto;
        $dto->instructor_id = $ctx->manager->ensureImported('instructor', $ctx->old->teacher_id, $ctx->level);

        return $next($ctx);
    }
}