<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Pipes\PersistEntity;
use App\Services\Import\Pipes\Tariff;

class TariffImporter extends ModelImporter
{

    /**
     * @return class-string<PipeInterface>[]
     */
    protected function pipes(): array
    {
        return [
            Tariff\SkipInactiveTariff::class,
            Tariff\MapTariffEntity::class,
            PersistEntity::class,
            Tariff\AttachCourses::class,
        ];
    }
}