<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Pipes\Branch;
use App\Services\Import\Pipes\PersistEntity;

class BranchImporter extends ModelImporter
{

    protected function pipes(): array
    {
        return [
            Branch\MapBranchEntity::class,
            PersistEntity::class,
        ];
    }
}