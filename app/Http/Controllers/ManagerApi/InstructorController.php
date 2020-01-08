<?php
/**
 * File: InstructorController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\AttachInstructorRequest;
use App\Http\Requests\ManagerApi\StoreInstructorRequest;
use App\Http\Requests\ManagerApi\UpdateInstructorRequest;
use App\Http\Resources\InstructorResource;
use App\Models\Instructor;
use App\Repository\InstructorRepository;
use App\Repository\PersonRepository;
use Illuminate\Support\Facades\DB;

class InstructorController extends Controller
{
    private PersonRepository $personRepository;

    /**
     * @var InstructorRepository
     */
    private InstructorRepository $instructorRepository;

    public function __construct(InstructorRepository $instructorRepository, PersonRepository $personRepository)
    {
        $this->instructorRepository = $instructorRepository;
        $this->personRepository = $personRepository;
    }

    public function store(StoreInstructorRequest $request): InstructorResource
    {
        /** @var Instructor $instructor */
        $instructor = DB::transaction(function () use ($request) {
            $person = $this->personRepository->create($request->getPersonDto());
            return $this->instructorRepository->createFromPerson($person, $request->getInstructorDto());
        });
        $instructor->load('person');

        return new InstructorResource($instructor);
    }

    /**
     * @param AttachInstructorRequest $request
     * @return InstructorResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function createFromPerson(AttachInstructorRequest $request): InstructorResource
    {
        $instructor = $request->getDto();
        $person = $this->personRepository->find($request->person_id);
        $instructor = $this->instructorRepository->createFromPerson($person, $instructor);
        $instructor->load('person');

        return new InstructorResource($instructor);
    }

    /**
     * @param string $id
     * @return InstructorResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $id): InstructorResource
    {
        $instructor = $this->instructorRepository->find($id);
        $instructor->load('person');

        return new InstructorResource($instructor);
    }

    /**
     * @param string $id
     * @param UpdateInstructorRequest $request
     * @return InstructorResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(string $id, UpdateInstructorRequest $request): InstructorResource
    {
        $instructor = $this->instructorRepository->find($id);
        $this->instructorRepository->update($instructor, $request->getDto());
        $instructor->load('person');

        return new InstructorResource($instructor);
    }

    /**
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $instructor = $this->instructorRepository->find($id);
        $this->instructorRepository->delete($instructor);
    }
}
