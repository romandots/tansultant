<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Pipes\PersistEntity;
use App\Services\Import\Pipes\Subscription;

class SubscriptionImporter extends ModelImporter
{

    /**
     * @return class-string<PipeInterface>[]
     */
    protected function pipes(): array
    {
        return [
            Subscription\SkipExpiredTickets::class,
            Subscription\MapSubscriptionEntity::class,
            Subscription\ResolveSubscriptionRelations::class,
            PersistEntity::class,
            Subscription\CreateHoldForSubscription::class,
        ];
    }
}