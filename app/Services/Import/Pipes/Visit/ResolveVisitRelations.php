<?php

namespace App\Services\Import\Pipes\Visit;

use App\Components\Visit\Dto;
use App\Models\Enum\VisitPaymentType;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class ResolveVisitRelations implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        /** @var Dto $dto */
        $dto = $ctx->dto;
        $dto->manager_id = $ctx->adminUser->id;
        $dto->student_id = $ctx->manager->ensureImported('student', $ctx->old->client_id, $ctx->level);
        $dto->event_id = $ctx->manager->ensureImported('lesson', $ctx->old->lesson_id, $ctx->level);

        if ($ctx->old->ticket_id) {
            $dto->subscription_id = $ctx->manager->ensureImported('subscription', $ctx->old->ticket_id, $ctx->level);
            $dto->pay_from_balance = false;
            $dto->payment_type = VisitPaymentType::SUBSCRIPTION;
        } else {
            $dto->price = 0;
            $dto->pay_from_balance = true;
            $dto->payment_type = VisitPaymentType::PAYMENT;
        }

        return $next($ctx);
    }
}