<?php

namespace App\Common;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use App\Common\DTO\SuggestDto;
use App\Common\Traits\WithCache;
use App\Components\Loader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseComponentService extends BaseService
{
    use WithCache;

    protected BaseComponentRepository $repository;
    protected \App\Components\LogRecord\Facade $history;

    public function __construct(
        protected string $modelClass,
        string $repositoryClass,
        protected string $dtoClass,
    ) {
        $this->repository = \app($repositoryClass);
        $this->history = Loader::logRecords();
    }

    public function suggest(
        SuggestDto $suggestDto,
        string|\Closure $labelField = 'name',
        string|\Closure $valueField = 'id',
        array $additionalFields = []
    ): array {
        $cacheKey = 'suggest_' . md5($suggestDto->query);
        $cached = $this->getFromCache($cacheKey);

        if (null !== $cached) {
            $this->debug('Suggest loaded from cache');
            return $cached;
        }

        $dto = new SearchFilterDto();
        $dto->query = $suggestDto->query;
        $dto->with_deleted = false;

        $result = $this->getRepository()
            ->getFilterQuery($dto)
            ->limit(10)
            ->get()
            ->map(function (Model $record) use ($additionalFields, $labelField, $valueField) {
                assert(is_a($record, $this->getModelClassName()));
                try {
                    $label = property_or_callback($record, $labelField);
                } catch (\Exception $exception) {
                    $label = (string)$record;
                }

                try {
                    $value = property_or_callback($record, $valueField);
                } catch (\Exception $exception) {
                    $value = $record?->id;
                }

                $set = [
                    'label' => $label,
                    'value' => $value,
                ];

                foreach ($additionalFields as $key => $field) {
                    try {
                        $fieldValue = property_or_callback($record, $field);
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

    public function search(SearchDto $searchParams, array $relations = [], array $countRelations = []): Collection
    {
        return $this->getRepository()->findFilteredPaginated($searchParams, $relations, $countRelations);
    }

    public function getMeta(SearchDto $searchParams): array
    {
        $totalRecords = $this->getRepository()->countFiltered($searchParams->filter);
        return $searchParams->getMeta($totalRecords);
    }

    public function find(string $id, array $relations = [], array $countRelations = [], bool $forceNoCache = false): Model
    {
        $cacheKey = 'record_' . $id;
        $cached = $forceNoCache ? null : $this->getFromCache($cacheKey);

        if (null !== $cached) {
            $this->debug('Model loaded from cache');
            return $cached;
        }

        $result = $this->getRepository()->find($id, $relations, $countRelations);
        $this->storeInCache($cacheKey, $result);

        return $result;
    }

    /**
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        assert($dto instanceof $this->dtoClass);
        try {
            $this->debug('Creating record of model ' . $this->modelClass, (array)$dto);
            $record = $this->getRepository()->create($dto);

            if ($dto->getUser()) {
                $this->history->logCreate($dto->getUser(), $record);
            }

            $this->debug('Record of model ' . $this->modelClass . ' is created with ID#' . $record->id, $record->toArray());
            return $record;
        } catch (\Throwable $exception) {
            $this->error('Failed creating record of model ' . $this->modelClass, (array)$dto);
            throw $exception;
        }
    }

    /**
     * @throws \Throwable
     */
    public function update(Model $record, Contracts\DtoWithUser $dto): void
    {
        assert($dto instanceof $this->dtoClass);
        try {
            $this->debug('Updating record #' . $record->id . ' of model ' . $this->modelClass, (array)$dto);
            $originalRecord = clone $record;
            $this->getRepository()->update($record, $dto);

            if ($dto->getUser()) {
                $this->history->logUpdate($dto->getUser(), $record, $originalRecord);
            }

            $this->debug('Record of model #' . $record->id . ' of model ' . $this->modelClass . ' is updated', $record->toArray());
        } catch (\Throwable $exception) {
            $this->error('Failed updating record #' . $record->id . ' of model ' . $this->modelClass, (array)$dto);
            throw $exception;
        }
    }

    /**
     * @throws \Throwable
     */
    public function delete(Model $record, \App\Models\User $user): void
    {
        try {
            $this->debug('Deleting record #' . $record->id . ' of model ' . $this->modelClass);
            $oldCopy = clone $record;
            $this->getRepository()->delete($record);
            $this->history->logDelete($user, $oldCopy);
            $this->debug('Record of model #' . $oldCopy->id . ' of model ' . $this->modelClass . ' is deleted');
            unset($oldCopy);
        } catch (\Throwable $exception) {
            $this->error('Failed deleting record #' . $record->id . ' of model ' . $this->modelClass);
            throw $exception;
        }
    }

    /**
     * @throws \Throwable
     */
    public function restore(Model $record, \App\Models\User $user): void
    {
        try {
            $this->debug('Restoring record #' . $record->id . ' of model ' . $this->modelClass);
            $this->getRepository()->restore($record);
            $this->history->logRestore($user, $record);
            $this->debug('Record of model #' . $record->id . ' of model ' . $this->modelClass . ' is restored');
        } catch (\Throwable $exception) {
            $this->error('Failed restoring record #' . $record->id . ' of model ' . $this->modelClass);
            throw $exception;
        }
    }

    public function getRepository(): BaseComponentRepository
    {
        return $this->repository;
    }

    public function getModelClassName(): string
    {
        return $this->modelClass;
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

    protected function error(string $message, array|\Throwable $context = []): void
    {
        if (!is_array($context)) {
            $context = [
                'message' => $context->getMessage(),
                'trace' => $context->getTrace(),
            ];
        }

        $this->getLogger()->error($message, $context);
    }

    protected function critical(string $message, array|\Throwable $context = []): void
    {
        if (!is_array($context)) {
            $context = [
                'message' => $context->getMessage(),
                'trace' => $context->getTrace(),
            ];
        }

        $this->getLogger()->critical($message, $context);
    }
}