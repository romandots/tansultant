<?php

namespace App\Services\Import\Maps;

use App\Components\Loader;
use Illuminate\Support\Collection;

class BranchesMap extends ObjectsMap
{
    protected function getPromptMessage(object $oldObject, ?string $additionalText = null): string
    {
        return "Which branch should be associated with studio {$oldObject->studio_title}";
    }

    protected function getMapCacheKey(): string
    {
        return 'studios_to_branches_map';
    }

    public function getNewObjects(): Collection
    {
        return Loader::branches()->getAll();
    }

    public function getOldObjects(): Collection
    {
        return $this->dbConnection
            ->table('studios')
            ->get(['id', 'studio_title']);
    }
}