<?php

namespace App\Common;

use App\Common\Contracts\PaginatedInterface;
use App\Http\Requests\DTO\FilteredDto;
use App\Services\WithCache;
use App\Services\WithLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseService
{
    use WithLogger;
    use WithCache;

    protected BaseRepository $repository;

    public function __construct(
        protected string $modelClass,
        string $repositoryClass,
        protected string $dtoClass,
        protected ?string $searchFilterDtoClass
    ) {
        $this->repository = \app($repositoryClass);
    }

    public function suggest(
        ?string $query,
        string|\Closure $labelField = 'name',
        string|\Closure $valueField = 'id',
        array $additionalFields = []
    ): array {
        $cacheKey = 'suggest_' . md5($query);
        $cached = $this->getFromCache($cacheKey);

        if (null !== $cached) {
            $this->debug('Suggest loaded from cache');
            return $cached;
        }

        $dto = $this->makeSearchFilterDto();
        $dto->query = $query;
        $dto->with_deleted = false;

        $result = $this->getRepository()
            ->getSuggestQuery($dto)
            ->limit(10)
            ->get()
            ->map(function (Model $record) use ($additionalFields, $labelField, $valueField) {
                assert(is_a($record, $this->getModelClassName()));
                try {
                    if ($labelField instanceof \Closure) {
                        $label = $labelField($record);
                    } else {
                        $label = $record->{$labelField};
                    }
                } catch (\Exception $exception) {
                    $label = (string)$record;
                }

                try {
                    if ($valueField instanceof \Closure) {
                        $value = $valueField($record);
                    } else {
                        $value = $record->{$valueField};
                    }
                } catch (\Exception $exception) {
                    $value = $record->id;
                }

                $set = [
                    'label' => $label,
                    'value' => $value,
                ];

                foreach ($additionalFields as $key => $field) {
                    try {
                        if ($field instanceof \Closure) {
                            $fieldValue = $field($record);
                        } else {
                            $fieldValue = $record->{$field};
                        }
                        $set[$key] = $fieldValue;
                    } catch (\Exception $exception) {
                    }
                }

                return $set;
            })
            ->toArray();

        $this->storeInCache($cacheKey, $result, 60);

        return $result;
    }

    public function search(PaginatedInterface $searchParams, array $relations = []): Collection
    {
        return $this->getRepository()->findFilteredPaginated($searchParams, $relations);
    }

    public function getMeta(PaginatedInterface $searchParams): array
    {
        $totalRecords = $this->getRepository()->countFiltered($searchParams->filter);
        return $searchParams->getMeta($totalRecords);
    }

    /**
     * @throws \Throwable
     */
    public function create(Contracts\Dto $storeDto): Model
    {
        assert($storeDto instanceof $this->dtoClass);
        try {
            $this->debug('Creating record of model ' . $this->modelClass, (array)$dto);
            $record = $this->getRepository()->create($storeDto);
            $this->debug('Record of model ' . $this->modelClass . ' is created with ID#' . $record->id, $record->toArray());
            return $record;
        } catch (\Throwable $exception) {
            $this->error('Failed creating record of model ' . $this->modelClass, (array)$storeDto);
            throw $exception;
        }
    }

    /**
     * @throws \Throwable
     */
    public function update(Model $record, Contracts\Dto $dto): void
    {
        assert($dto instanceof $this->dtoClass);
        try {
            $this->debug('Updating record #' . $record->id . ' of model ' . $this->modelClass, (array)$dto);
            $this->getRepository()->update($record, $dto);
            $this->debug('Record of model #' . $record->id . ' of model ' . $this->modelClass . ' is updated', $record->toArray());
        } catch (\Throwable $exception) {
            $this->error('Failed updating record #' . $record->id . ' of model ' . $this->modelClass, (array)$dto);
            throw $exception;
        }
    }

    /**
     * @throws \Throwable
     */
    public function delete(Model $record): void
    {
        try {
            $this->debug('Deleting record #' . $record->id . ' of model ' . $this->modelClass);
            $this->getRepository()->delete($record);
            $this->debug('Record of model #' . $record->id . ' of model ' . $this->modelClass . ' is deleted');
        } catch (\Throwable $exception) {
            $this->error('Failed deleting record #' . $record->id . ' of model ' . $this->modelClass);
            throw $exception;
        }
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }

    public function getModelClassName(): string
    {
        return $this->modelClass;
    }

    public function makeSearchFilterDto(): FilteredDto
    {
        return new $this->searchFilterDtoClass();
    }

    protected function getLoggerPrefix(): string
    {
        return \base_classname($this->getModelClassName());
    }

    protected function getCachePrefix(): string
    {
        return \base_classname($this->getModelClassName());
    }

    protected function debug(string $message, array $context = []): void
    {
        $this->getLogger()->debug($message, $context);
    }

    protected function error(string $message, array $context = []): void
    {
        $this->getLogger()->error($message, $context);
    }
}