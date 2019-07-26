<?php
/**
 * File: StudentRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Person;
use App\Models\Student;

/**
 * Class StudentRepository
 * @package App\Repository
 */
class StudentRepository
{
    /**
     * @param int $id
     * @return Student|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): ?Student
    {
        return Student::query()->findOrFail($id);
    }

    /**
     * @param Person $person
     * @param int|null $cardNumber
     * @return Student
     */
    public function create(Person $person, ?int $cardNumber = null): Student
    {
        $student = new Student;
        $student->person_id = $person->id;
        $student->name = "{$person->last_name} {$person->first_name}";
        $student->card_number = $cardNumber;
        $student->status = Student::STATUS_POTENTIAL;
        $student->save();

        return $student;
    }

    /**
     * @param Student $person
     * @param \App\Http\Requests\Api\DTO\Student $dto
     */
    public function update(Student $person, \App\Http\Requests\Api\DTO\Student $dto): void
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
