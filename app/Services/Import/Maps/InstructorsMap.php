<?php

namespace App\Services\Import\Maps;

use App\Components\Loader;
use Illuminate\Support\Collection;

class InstructorsMap extends ObjectsMap
{

    protected function getMapCacheKey(): string
    {
        return 'teachers_to_instructor_map';
    }

    protected function getPromptMessage(object $oldObject, ?string $additionalText = null): string
    {
        return "Which instructor should be associated with teacher {$oldObject->name} {$oldObject->lastname}";
    }

    public function getOldObjects(): Collection
    {
        return $this->dbConnection->table('teachers')->get();
    }

    public function getNewObjects(): Collection
    {
        return Loader::instructors()->getAll();
    }
}