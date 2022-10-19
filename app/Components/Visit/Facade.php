<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Visit;
use Illuminate\Support\Collection;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Visit> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Visit> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Visit create(Dto $dto, array $relations = [])
 * @method \App\Models\Visit find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Visit findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Visit findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @param Collection<Visit> $visits
     * @return bool
     */
    public function visitsArePaid(Collection $visits): bool
    {
        return $this->getManager()->visitsArePaid($visits);
    }

    protected function getManager(): Manager
    {
        return \app(Manager::class);
    }
}