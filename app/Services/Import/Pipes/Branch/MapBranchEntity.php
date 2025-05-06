<?php

namespace App\Services\Import\Pipes\Branch;

use App\Components\Branch\Dto;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class MapBranchEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $old = $ctx->old;
        $ctx->dto = new Dto();
        $ctx->dto->name = $old->studio_title;
        $ctx->dto->phone = $old->phone;
        $ctx->dto->description = $old->description;

        return $next($ctx);
    }
}