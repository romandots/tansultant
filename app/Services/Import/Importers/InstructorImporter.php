<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Pipes\Instructor;
use App\Services\Import\Pipes\PersistEntity;

class InstructorImporter extends ModelImporter

{

    /**
     * @return class-string<PipeInterface>[]
     */
    protected function pipes(): array
    {
        return [
            Instructor\MapInstructorEntity::class,
            Instructor\MapInstructorPersonEntity::class,
            PersistEntity::class,
        ];
    }
}