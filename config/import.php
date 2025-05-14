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
            'where' => "status IN ('staff', 'exclusive')",
            'model' => Models\Instructor::class,
            'importer' => Importers\InstructorImporter::class,
            'service' => Components\Instructor\Service::class,
        ],
        'student' => [
            'table' => 'clients',
            'where' => "DATE(last_visit) >= '2023-01-01'",
            'model' => Models\Student::class,
            'importer' => Importers\StudentImporter::class,
            'service' => Components\Student\Service::class,
        ],
        'course' => [
            'table' => 'classes',
            'where' => '(deleted IS NULL OR deleted = 0) AND (end_date IS NULL OR end_date > NOW()) AND teacher_id IS NOT NULL',
            'model' => Models\Course::class,
            'importer' => Importers\CourseImporter::class,
            'service' => Components\Course\Service::class,
        ],
        'tariff' => [
            'table' => 'ticket_types',
            'where' => 'ticket_type_active = 1',
            'model' => Models\Tariff::class,
            'importer' => Importers\TariffImporter::class,
            'service' => Components\Tariff\Service::class,
        ],
        'subscription' => [
            'table' => 'tickets',
            'where' => "expired > NOW() OR status NOT IN ('expired', 'null')",
            'model' => Models\Subscription::class,
            'importer' => Importers\SubscriptionImporter::class,
            'service' => Components\Subscription\Service::class,
        ],
    ],
];