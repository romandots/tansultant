<?php

namespace App\Services\Instructor;

use App\Http\Requests\DTO\StoreInstructor;
use App\Models\Instructor;
use App\Repository\InstructorRepository;
use App\Services\BaseService;

class InstructorFacade extends \App\Services\BaseFacade
{
    protected InstructorService $service;

    public function __construct(InstructorRepository $repository, InstructorService $service)
    {
        $this->service = $service;
    }

    public function getService(): BaseService
    {
        return $this->service;
    }

    public function create(\App\Http\Requests\DTO\StoreInstructor $storeInstructor): Instructor
    {
        return $this->service->create($storeInstructor);
    }

    public function findAndUpdate(string $id, StoreInstructor $storeInstructor): Instructor
    {
        $instructor = $this->getRepository()->find($id);
        assert($instructor instanceof Instructor);
        $this->service->update($instructor, $storeInstructor);
        $instructor->load('person');

        return $instructor;
    }
}