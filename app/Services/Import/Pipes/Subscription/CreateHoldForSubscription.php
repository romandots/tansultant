<?php

namespace App\Services\Import\Pipes\Subscription;

use App\Components\Hold\Dto;
use App\Components\Loader;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Closure;

class CreateHoldForSubscription implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        if ($ctx->old->status !== 'frozen' || $ctx->old->unfrozen !== null || $ctx->old->frozen === null) {
            return $next($ctx);
        }

        $dto = new Dto();
        $dto->subscription_id = $ctx->newId;

        try {
            $dto->starts_at = Carbon::createFromFormat('Y-m-d', $ctx->old->frozen);
        } catch(InvalidFormatException) {
            throw new ImportException('Нераспознанная дата заморозки: ' . $ctx->old->frozen);
        }

        try {
            $hold = Loader::holds()->create($dto);
        } catch (\Throwable $e) {
            throw new ImportException('Ошибка создания заморозки: ' . $e->getMessage(), $ctx->toArray());
        }

        $ctx->debug('Создали заморозку → #' . $hold->id);
        $ctx->manager->increaseCounter('hold');

        return $next($ctx);
    }
}