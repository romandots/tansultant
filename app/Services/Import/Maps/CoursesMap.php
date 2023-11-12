<?php

namespace App\Services\Import\Maps;

use App\Components\Loader;
use Illuminate\Support\Collection;

class CoursesMap extends ObjectsMap
{

    protected function getMapCacheKey(): string
    {
        return 'classes_to_courses_map';
    }

    protected function getPromptMessage(object $oldObject, ?string $additionalText = null): string
    {
        return "Which course should be associated with class {$oldObject->class_name}";
    }

    public function getOldObjects(): Collection
    {
        return $this->dbConnection->table('classes')->get();
    }

    public function getNewObjects(): Collection
    {
       return Loader::courses()->getAll();
    }
}