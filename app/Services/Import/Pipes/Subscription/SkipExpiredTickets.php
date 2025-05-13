<?php

namespace App\Services\Import\Pipes\Subscription;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportSkippedException;
use App\Services\Import\ImportContext;
use Closure;

class SkipExpiredTickets implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        if ($ctx->old->status === 'expired' || strtotime($ctx->old->expired) < time()) {
            throw new ImportSkippedException('Абонемент просрочен');
        }
        if ($ctx->old->status === 'null') {
            throw new ImportSkippedException('Абонемент аннулирован');
        }

        return $next($ctx);
    }
}