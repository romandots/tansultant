<?php

declare(strict_types=1);

namespace App\Components\Shift;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\User;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Shift> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Shift> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Shift create(Dto $dto, array $relations = [])
 * @method \App\Models\Shift find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Shift findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Shift findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function closeActiveShift(User $user): Formatter
    {
        $shift = $this->getService()->closeUserActiveShift($user);
        return new Formatter($shift);
    }
}