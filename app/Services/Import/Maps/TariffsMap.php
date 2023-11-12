<?php

namespace App\Services\Import\Maps;

use App\Components\Loader;
use Illuminate\Support\Collection;

class TariffsMap extends ObjectsMap
{

    protected function getMapCacheKey(): string
    {
        return 'ticket_types_to_tariffs_map';
    }

    protected function getPromptMessage(object $oldObject, ?string $additionalText = null): string
    {
        return "Which tariff should be associated with ticket type {$oldObject->ticket_type_name} ({$oldObject->id})";
    }

    public function getOldObjects(): Collection
    {
        return $this->dbConnection->table('ticket_types')->get();
    }

    public function getNewObjects(): Collection
    {
        return Loader::tariffs()->getAll();
    }
}