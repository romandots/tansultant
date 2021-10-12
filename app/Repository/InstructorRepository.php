<?php
/**
 * File: InstructorRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\DTO\Contracts\FilteredInterface;
use App\Http\Requests\DTO\StoreInstructor;
use App\Http\Requests\ManagerApi\DTO\SearchInstructorsFilterDto;
use App\Models\Instructor;
use App\Models\Person;
use Carbon\Carbon;

/**
 * Class InstructorRepository
 * @package App\Repository
 */
class InstructorRepository extends BaseRepository
{
    public const WITH_SOFT_DELETES = true;
    public const SEARCHABLE_ATTRIBUTES = [
        'name',
        'description',
    ];

    public function getSearchableAttributes(): array
    {
        return self::SEARCHABLE_ATTRIBUTES;
    }

    public function withSoftDeletes(): bool
    {
        return self::WITH_SOFT_DELETES;
    }

    public function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Instructor::query();
    }

    /**
     * @param FilteredInterface|SearchInstructorsFilterDto $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getFilterQuery(FilteredInterface $filter): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getFilterQuery($filter);

        if (isset($filter->statuses) && !empty($filter->statuses)) {
            $query->whereIn('status', $filter->statuses);
        }

        if (isset($filter->display) && !empty($filter->display)) {
            $query->where('display', $filter->display);
        }

        return $query;
    }

    /**
     * @param Person $person
     * @param StoreInstructor $dto
     * @return Instructor
     * @throws \Exception
     */
    public function createFromPerson(Person $person, StoreInstructor $dto): Instructor
    {
        $instructor = new Instructor();
        $instructor->id = \uuid();
        $instructor->created_at = Carbon::now();
        $instructor->updated_at = Carbon::now();

        $instructor->person_id = $person->id;
        $instructor->name = $dto->name ?? \trans('person.instructor_name', $person->compactName());
        $instructor->description = $dto->description;
        $instructor->display = $dto->display;
        $instructor->status = $dto->status ?: Instructor::STATUS_HIRED;

        $instructor->save();

        return $instructor;
    }

    /**
     * @param Instructor $instructor
     * @param \App\Http\Requests\DTO\StoreInstructor $dto
     * @return void
     */
    public function update(Instructor $instructor, \App\Http\Requests\DTO\StoreInstructor $dto): void
    {
        $instructor->description = $dto->description;
        $instructor->display = $dto->display;
        if (null !== $dto->status) {
            $instructor->status = $dto->status;
        }
        $instructor->save();
    }
}
