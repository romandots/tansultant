<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Pipes\Branches;

class BranchImporter extends ModelImporter
{

    protected function pipes(): array
    {
        return [
            Branches\MapBranchEntity::class,
        ];
    }
}