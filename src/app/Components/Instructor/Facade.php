<?php

declare(strict_types=1);

namespace App\Components\Instructor;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Instructor;
use App\Models\Person;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Instructor> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Instructor> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Instructor create(Dto $dto, array $relations = [])
 * @method \App\Models\Instructor find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Instructor findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Instructor findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createFromPerson(Dto $dto, Person $person): Instructor
    {
        return $this->getService()->createFromPerson($dto, $person);
    }

    public function getInvolvedInstructorsIdsForBranchAndPeriod(
        string $branchId,
        \Carbon\Carbon $from,
        \Carbon\Carbon $to
    ): array {
        return $this->getRepository()->getInvolvedInstructorsIdsForBranchAndPeriod($branchId, $from, $to);
    }
}