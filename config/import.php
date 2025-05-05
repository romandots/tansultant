<?php

use App\Models;
use App\Services\Import\Importers;

return [
    'map' => [
        'accounts' => [
            'model' => Models\Account::class,
        ],
        'students' => [
            'table' => 'clients',
            'model' => Models\Student::class,
            'importer' => Importers\StudentImporter::class
        ],
    ],
];