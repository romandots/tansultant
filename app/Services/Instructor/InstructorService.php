<?php

namespace App\Services\Instructor;

use App\Http\Requests\DTO\FilteredDto;
use App\Http\Requests\ManagerApi\DTO\SearchInstructorsFilterDto;
use App\Models\Instructor;
use App\Repository\BaseRepository;
use App\Repository\InstructorRepository;
use App\Repository\PersonRepository;
use Illuminate\Validation\Rules\In;

class InstructorService extends \App\Services\BaseService
{
    protected InstructorRepository $repository;
    protected PersonRepository $personRepository;

    public function __construct(InstructorRepository $repository, PersonRepository $personRepository)
    {
        $this->repository = $repository;
        $this->personRepository = $personRepository;
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }

    public function getModelClassName(): string
    {
        return Instructor::class;
    }

    public function makeSearchFilterDto(): FilteredDto
    {
        $searchInstructorsFilterDto = new SearchInstructorsFilterDto();
        $searchInstructorsFilterDto->statuses = [
            Instructor::STATUS_FREELANCE,
            Instructor::STATUS_HIRED,
        ];
        return $searchInstructorsFilterDto;
    }

    public function create(\App\Http\Requests\DTO\StoreInstructor $storeInstructor): Instructor
    {
        $person = $this->personRepository->find($request->person_id);
        $instructor = $this->repository->createFromPerson($person, $storeInstructor);
        $instructor->load('person');

        return $instructor;
    }

    public function update(Instructor $instructor, \App\Http\Requests\DTO\StoreInstructor $storeInstructor): void
    {
        $this->repository->update($instructor, $storeInstructor);
    }
}