<?php
/**
 * File: StudentController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AttachStudentRequest;
use App\Http\Requests\Api\StoreStudentRequest;
use App\Http\Requests\Api\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Repository\PersonRepository;
use App\Repository\StudentRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class StudentController
 * @package App\Http\Controllers\Api
 */
class StudentController extends Controller
{
    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * StudentController constructor.
     * @param StudentRepository $studentRepository
     * @param PersonRepository $personRepository
     */
    public function __construct(StudentRepository $studentRepository, PersonRepository $personRepository)
    {
        $this->studentRepository = $studentRepository;
        $this->personRepository = $personRepository;
    }

    /**
     * @param StoreStudentRequest $request
     * @return StudentResource
     */
    public function store(StoreStudentRequest $request): StudentResource
    {
        /** @var Student $student */
        $student = DB::transaction(function () use ($request) {
            $person = $this->personRepository->create($request->getPersonDto());
            return $this->studentRepository->create($person, $request->getStudentDto()->card_number);
        });
        $student->load('customer', 'person');

        return new StudentResource($student);
    }

    /**
     * @param AttachStudentRequest $request
     * @return StudentResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function createFromPerson(AttachStudentRequest $request): StudentResource
    {
        $attachStudent = $request->getDto();
        $person = $this->personRepository->find($attachStudent->person_id);
        $student = $this->studentRepository->create($person, $attachStudent->card_number);
        $student->load('customer', 'person');

        return new StudentResource($student);
    }

    /**
     * @param int $id
     * @return StudentResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(int $id): StudentResource
    {
        $student = $this->studentRepository->find($id);
        $student->load('customer', 'person');

        return new StudentResource($student);
    }

    /**
     * @param int $id
     * @param UpdateStudentRequest $request
     * @return StudentResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, UpdateStudentRequest $request): StudentResource
    {
        $student = $this->studentRepository->find($id);
        $this->studentRepository->update($student, $request->getDto());
        $student->load('customer', 'person');

        return new StudentResource($student);
    }

    /**
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(int $id): void
    {
        $student = $this->studentRepository->find($id);
        $this->studentRepository->delete($student);
    }
}
