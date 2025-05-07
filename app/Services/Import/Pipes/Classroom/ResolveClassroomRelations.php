<?php

namespace App\Services\Import\Pipes\Classroom;

use App\Components\Classroom\Dto;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class ResolveClassroomRelations implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        if ($ctx->old->studio_id) {
            /** @var Dto $dto */
            $dto = $ctx->dto;
            $dto->branch_id = $ctx->manager->ensureImported('branch', $ctx->old->studio_id, $ctx->level);
        }

        return $next($ctx);
    }
}