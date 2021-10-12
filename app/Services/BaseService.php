<?php

namespace App\Services;

use App\Http\Requests\DTO\Contracts\PaginatedInterface;
use App\Http\Requests\DTO\FilteredDto;
use App\Repository\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseService
{
    use WithLogger, WithCache;

    abstract public function getRepository(): BaseRepository;
    abstract public function getModelClassName(): string;
    abstract public function makeSearchFilterDto(): FilteredDto;

    protected function getLoggerPrefix(): string
    {
        return base_classname($this->getModelClassName());
    }

    protected function getCachePrefix(): string
    {
        return base_classname($this->getModelClassName());
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
}