<?php

namespace App\Services\Import\Maps;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

abstract class ObjectsMap
{
    protected array $map;
    protected Collection $oldObjects;
    protected Collection $newObjects;
    protected \Illuminate\Console\Command $cli;
    protected \Illuminate\Database\Connection $dbConnection;

    public function __construct(
    ) {
        //$this->loadMap();
    }

    public function buildMap(): void
    {
        $newObjectsKeys = $this->newObjects->pluck('id', 'name')->toArray();
        $newObjectsValues = $this->newObjects->pluck('name')->toArray();
        foreach ($this->oldObjects as $oldObject) {
            if (count($newObjectsValues) < 1) {
                break;
            }

            $pickedValue = $this->cli->choice(
                $this->getPromptMessage($oldObject),
                $newObjectsValues + [count($newObjectsValues) => '--']
            );
            $pickedId = $newObjectsKeys[$pickedValue] ?? null;

            if ($pickedId === null) {
                continue;
            }

            $this->map($oldObject->id, $pickedId);
            $pickedKey = array_search($pickedValue, $newObjectsValues, true);
            unset($newObjectsValues[$pickedKey], $newObjectsKeys[$pickedValue]);
        }
    }

    public function getMap(): array
    {
        $this->loadMap();
        return $this->map;
    }

    public function mappedRecord(int|string $oldId): ?Model
    {
        $mappedId = $this->mapped($oldId);
        if (null === $mappedId) {
            return null;
        }

        $newRecord = $this->loadNewObject($mappedId);
        if (null === $newRecord) {
            $this->removeMapped($mappedId);
        }

        return $newRecord;
    }

    public function mapped(int|string $oldObjectId): ?string
    {
        return $this->getMap()[$oldObjectId] ?? null;
    }

    public function map(int|string $oldObjectId, string $newObjectId): void
    {
        $this->loadMapFromCache();
        $this->map[$oldObjectId] = $newObjectId;
        $this->saveMapToCache();
    }

    public function removeMapped(int|string $oldId): void
    {
        $this->loadMapFromCache();
        unset($this->map[$oldId]);
        $this->saveMapToCache();
    }

    protected function loadMapFromCache(): void
    {
        $this->map = Cache::get($this->getMapCacheKey(), []);
    }

    protected function saveMapToCache(): void
    {
        Cache::set($this->getMapCacheKey(), $this->map);
    }

    public function __toArray(): array
    {
        return $this->getMap();
    }

    public function isMapEmpty(): bool
    {
        return count($this->getMap()) < 1;
    }

    public function nextNumber(): int
    {
        return count($this->getMap()) + 1;
    }

    abstract protected function getMapCacheKey(): string;
    abstract protected function getPromptMessage(object $oldObject, ?string $additionalText = null): string;
    abstract public function getOldObjects(): Collection;
    abstract public function getNewObjects(): Collection;

    public function loadOldObject(int|string $oldId): ?\stdClass
    {
        return $this->oldObjects->firstWhere('id', $oldId);
    }

    public function loadNewObject(string $newId): ?Model
    {
        return $this->newObjects->firstWhere('id', $newId);
    }

    public function loadMap(): void
    {
        if (!isset($this->map)) {
            $this->loadMapFromCache();
        }

        try {
            if (!isset($this->oldObjects)) {
                $this->oldObjects = $this->getOldObjects();
            }
        } catch (\LogicException) {
        } catch (\Throwable $e) {
            throw $e;
        }

        try {
            if (!isset($this->newObjects)) {
                $this->newObjects = $this->getNewObjects();
            }
        } catch (\LogicException) {
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function setCli(\Illuminate\Console\Command $cli): ObjectsMap
    {
        $this->cli = $cli;
        return $this;
    }

    public function setDbConnection(\Illuminate\Database\Connection $dbConnection): ObjectsMap
    {
        $this->dbConnection = $dbConnection;
        return $this;
    }
}