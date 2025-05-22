<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Pipes\Lesson;
use App\Services\Import\Pipes\PersistEntity;

class LessonImporter extends ModelImporter
{
    /**
     * @return class-string<PipeInterface>[]
     */
    protected function pipes(): array
    {
        return [
            Lesson\SkipPendingLessons::class,
            Lesson\MapLessonEntity::class,
            Lesson\ResolveLessonRelations::class,
            PersistEntity::class,
        ];
    }
}

