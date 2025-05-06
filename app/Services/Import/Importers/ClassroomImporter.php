<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Pipes\Classroom;

class ClassroomImporter extends ModelImporter
{

    /**
     * @return class-string<PipeInterface>[]
     */
    protected function pipes(): array
    {
        return [
            Classroom\MapClassroomEntity::class,
            Classroom\ResolveClassroomRelations::class,
        ];
    }
}