<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Pipes\Student;

class StudentImporter extends ModelImporter
{

    protected function pipes(): array
    {
        return [
            Student\CreateStudentPersonEntity::class,
            Student\CreateStudentCustomerEntity::class,
            Student\CreateStudentEntity::class,
        ];
    }
}