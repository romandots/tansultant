<?php

declare(strict_types=1);

namespace App\Components\Classroom;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Classroom> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Classroom> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Classroom create(Dto $dto, array $relations = [])
 * @method \App\Models\Classroom find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Classroom findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Classroom findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @param string $branchId
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Classroom>
     */
    public function getByBranchId(string $branchId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getRepository()->getByBranchId($branchId);
    }

    public function getBranchIdByClassroomId(string $classroomId): string
    {
        return $this->getRepository()->getBranchIdByClassroomId($classroomId);
    }
}