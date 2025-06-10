<?php

namespace App\Services\Import\Pipes\Visit;

use App\Components\Visit\Dto;
use App\Models\Enum\VisitEventType;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class MapVisitEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $ctx->dto = new Dto($ctx->adminUser);
        $ctx->dto->event_type = VisitEventType::LESSON;

        return $next($ctx);
    }
}