<?php

namespace App\Services\Import\Maps;

use Illuminate\Support\Collection;

class SubscriptionsMap extends ObjectsMap
{

    protected function getMapCacheKey(): string
    {
        return 'tickets_to_subscriptions_map';
    }

    protected function getPromptMessage(object $oldObject, ?string $additionalText = null): string
    {
        return "Which subscription should be associated with ticket {$oldObject->ticket_name} ({$oldObject->id})";
    }

    public function getOldObjects(): Collection
    {
        throw new \LogicException('Not implemented');
        //return $this->dbConnection->table('tickets')->get();
    }

    public function getNewObjects(): Collection
    {
        throw new \LogicException('Not implemented');
        //return Loader::subscriptions()->getAll();
    }
}