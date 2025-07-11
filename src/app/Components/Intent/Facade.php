<?php

declare(strict_types=1);

namespace App\Components\Intent;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Intent> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Intent> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Intent create(Dto $dto, array $relations = [])
 * @method \App\Models\Intent find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Intent findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Intent findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function updateIntents(Collection $visits, Collection $intents): void
    {
        $this->getService()->updateIntents($visits, $intents);
    }
}