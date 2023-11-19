<?php

namespace App\Services\Import\Maps;

use App\Components\Loader;
use Illuminate\Support\Collection;

class StudentsMap extends ObjectsMap
{

    protected function getMapCacheKey(): string
    {
        return 'clients_to_students_map';
    }

    protected function getPromptMessage(object $oldObject, ?string $additionalText = null): string
    {
        return "Which student should be associated with client {$oldObject->name} {$oldObject->last_name} ({$oldObject->id})";
    }

    public function getOldObjects(): Collection
    {
        return $this->dbConnection->table('clients')->get();
    }

    public function getNewObjects(): Collection
    {
        return Loader::students()->getAll();
    }
}