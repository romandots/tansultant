<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Pipes\Course;
use App\Services\Import\Pipes\PersistEntity;

class CourseImporter extends ModelImporter
{

    /**
     * @return class-string<PipeInterface>[]
     */
    protected function pipes(): array
    {
        return [
            Course\SkipDeletedAndInactiveCourse::class,
            Course\MapCourseEntity::class,
            Course\ResolveCourseRelations::class,
            Course\CreateCourseFormula::class,
            PersistEntity::class,
            Course\CreateCourseSlots::class,
        ];
    }
}