<?php
/**
 * File: StudentRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\DTO\StoreStudent;
use App\Models\Person;
use App\Models\Student;
use Carbon\Carbon;

/**
 * Class StudentRepository
 * @package App\Repository
 */
class StudentRepository
{
    /**
     * @param string $id
     * @return Student|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): ?Student
    {
        return Student::query()->findOrFail($id);
    }

    /**
     * @param Person $person
     * @param StoreStudent $storeStudent
     * @return Student
     * @throws \Exception
     */
    public function createFromPerson(Person $person, StoreStudent $storeStudent): Student
    {
        $student = new Student();

        $student->id = \uuid();
        $student->created_at = Carbon::now();
        $student->updated_at = Carbon::now();

        $student->name = "{$person->last_name} {$person->first_name}";
        $student->person_id = $person->id;

        $student->card_number = $storeStudent->card_number;
        $student->status = Student::STATUS_POTENTIAL;

        $student->save();

        return $student;
    }

    /**
     * @param Student $person
     * @param \App\Http\Requests\DTO\StoreStudent $dto
     */
    public function update(Student $person, \App\Http\Requests\DTO\StoreStudent $dto): void
    {
        $person->card_number = $dto->card_number;
        $person->save();
    }

    /**
     * @param Student $student
     * @throws \Exception
     */
    public function delete(Student $student): void
    {
        $student->delete();
    }
}
