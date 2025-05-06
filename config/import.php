<?php

use App\Components;
use App\Models;
use App\Services\Import\Importers;

return [
    'map' => [
        'branch' => [
            'table' => 'studios',
            'model' => Models\Branch::class,
            'importer' => Importers\BranchImporter::class,
            'service' => Components\Branch\Service::class,
        ],
        'classroom' => [
            'table' => 'dancefloors',
            'model' => Models\Classroom::class,
            'importer' => Importers\ClassroomImporter::class,
            'service' => Components\Classroom\Service::class,
        ],
        'instructor' => [
            'table' => 'teachers',
            'model' => Models\Instructor::class,
            'importer' => Importers\InstructorImporter::class,
            'service' => Components\Instructor\Service::class,
        ],
        'student' => [
            'table' => 'clients',
            'model' => Models\Student::class,
            'importer' => Importers\StudentImporter::class,
            'service' => Components\Student\Service::class,
        ],
    ],
];