<?php

declare(strict_types=1);

namespace App\Components\Person;

use App\Common\BaseComponentFacade;
use App\Models\Enum\Gender;
use App\Models\Person;
use Carbon\Carbon;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Person> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Person> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\Person create(Dto $dto, array $relations = [])
 * @method \App\Models\Person find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Person findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Person findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function getByPhoneNumber(string $phoneNumber): ?Person
    {
        return $this->getRepository()->getByPhoneNumber($phoneNumber);
    }

    public function getByNameGenderAndBirthDate(
        string $lastName,
        string $firstName,
        string $patronymicName,
        Gender $gender,
        Carbon $birthDate
    ): ?Person
    {
        return $this->getRepository()->getByNameGenderAndBirthDate($lastName, $firstName, $patronymicName, $gender, $birthDate);
    }
}