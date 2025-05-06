<?php

namespace App\Services\Import\Pipes\Classroom;

use App\Components\Classroom\Dto;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class MapClassroomEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $ctx->dto = new Dto();
        $ctx->dto->name = $ctx->old->name;
        $ctx->dto->color = $ctx->old->color;

        return $next($ctx);
    }
}