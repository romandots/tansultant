<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Pipes\PersistEntity;
use \App\Services\Import\Pipes\Visit;

class VisitImporter extends ModelImporter
{

    protected function pipes(): array
    {
        return [
            Visit\SkipExpiredVisits::class,
            Visit\MapVisitEntity::class,
            Visit\ResolveVisitRelations::class,
            PersistEntity::class,
        ];
    }
}