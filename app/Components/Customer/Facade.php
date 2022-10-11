<?php

declare(strict_types=1);

namespace App\Components\Customer;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Customer;
use App\Models\Person;
use App\Models\Student;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Customer> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Customer> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Customer create(Dto $dto, array $relations = [])
 * @method \App\Models\Customer find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Customer findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Customer findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createFromPerson(Dto $dto, Person $person): Customer
    {
        return $this->getService()->createFromPerson($dto, $person);
    }

    public function checkStudentFunds(Student $student, int $amount): bool
    {
        return $this->getService()->checkStudentCredits($student, $amount);
    }
}