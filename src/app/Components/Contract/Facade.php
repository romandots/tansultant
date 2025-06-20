<?php

declare(strict_types=1);

namespace App\Components\Contract;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Contract;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Contract> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Contract> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Contract create(Dto $dto, array $relations = [])
 * @method \App\Models\Contract find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Contract findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Contract findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function findByCustomerId(string $customerId): ?Contract
    {
        return $this->getRepository()->findByCustomerId($customerId);
    }

    public function sign(Contract $contract): void
    {
        $this->getService()->sign($contract);
    }

    public function terminate(Contract $contract): void
    {
        $this->getService()->terminate($contract);
    }
}