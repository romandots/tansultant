<?php
/**
 * File: InstructorRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Instructor;
use App\Models\Person;

/**
 * Class InstructorRepository
 * @package App\Repository
 */
class InstructorRepository
{
    /**
     * @param string $id
     * @return Instructor|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): ?Instructor
    {
        return Instructor::query()->findOrFail($id);
    }

    /**
     * @param Person $person
     * @param \App\Http\Requests\Api\DTO\Instructor $dto
     * @return Instructor
     * @throws \Exception
     */
    public function create(Person $person, \App\Http\Requests\Api\DTO\Instructor $dto): Instructor
    {
        $instructor = new Instructor;
        $instructor->id = \uuid();
        $instructor->person_id = $person->id;
        $instructor->name = "{$person->last_name} {$person->first_name}";
        $instructor->description = $dto->description;
        $instructor->display = $dto->display;
        $instructor->status = $dto->status ?: Instructor::STATUS_HIRED;
        $instructor->save();

        return $instructor;
    }

    /**
     * @param Instructor $instructor
     * @param \App\Http\Requests\Api\DTO\Instructor $dto
     * @return void
     */
    public function update(Instructor $instructor, \App\Http\Requests\Api\DTO\Instructor $dto): void
    {
        $instructor->description = $dto->description;
        $instructor->display = $dto->display;
        if (null !== $dto->status) {
            $instructor->status = $dto->status;
        }
        $instructor->save();
    }

    /**
     * @param Instructor $instructor
     * @throws \Exception
     */
    public function delete(Instructor $instructor): void
    {
        $instructor->delete();
    }
}
