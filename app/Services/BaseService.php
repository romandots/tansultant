<?php

namespace App\Services;

use App\Http\Requests\DTO\FilteredDto;
use App\Repository\Repository;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    use WithLogger;

    abstract public function getRepository(): Repository;
    abstract public function getModelClassName(): string;
    abstract public function makeSearchFilterDto(): FilteredDto;

    public function suggest(
        ?string $query,
        string|\Closure $labelField = 'name',
        string|\Closure $valueField = 'id',
        array $additionalFields = []
    ): array {
        $dto = $this->makeSearchFilterDto();
        $dto->query = $query;
        $dto->with_deleted = false;
        $records = $this->getRepository()->findFiltered($dto);

        if (is_string($labelField) && is_string($valueField) && [] === $additionalFields) {
            return $records->pluck($labelField, $valueField)->toArray();
        }

        return $records
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
    }
}