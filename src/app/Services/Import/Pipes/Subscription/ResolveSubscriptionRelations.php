<?php

namespace App\Services\Import\Pipes\Subscription;

use App\Components\Subscription\Dto;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class ResolveSubscriptionRelations implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        /** @var Dto $dto */
        $dto = $ctx->dto;

        if ($ctx->old->ticket_type) {
            $dto->tariff_id = $ctx->manager->ensureImported('tariff', $ctx->old->ticket_type, $ctx->level);
        }

        if ($ctx->old->client_id) {
            $dto->student_id = $ctx->manager->ensureImported('student', $ctx->old->client_id, $ctx->level);
        }

        return $next($ctx);
    }
}