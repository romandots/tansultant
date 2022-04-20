<?php

namespace Tests\Unit\Repository;

use App\Common\BaseComponentRepository;
use App\Common\Contracts\DtoWithUser;
use Tests\TestCase;

/**
 * Set up all repository tests here
 */
class RepositoryTest extends TestCase
{
    protected string $table;
    protected BaseComponentRepository $repository;
    protected string $dtoClass;

    protected function buildDto(array $attributes): DtoWithUser
    {
        $dto = new $this->dtoClass();
        foreach ($attributes as $key => $value) {
            $dto->{$key} = $value;
        }
        return $dto;
    }

    protected function _testCreate(array $attributes): void
    {
        $dto = $this->buildDto($attributes);

        $this->assertDatabaseMissing($this->table, $attributes);

        $record = $this->repository->create($dto);
        $attributes['id'] = $record->id;

        $this->assertDatabaseHas($this->table, $attributes);
    }

    protected function _testUpdate(array $originalAttributes, array $updatedAttributes): void
    {
        $createDto = $this->buildDto($originalAttributes);
        $updateDto = $this->buildDto($updatedAttributes);

        $record = $this->repository->create($createDto);
        $originalAttributes['id'] = $record->id;
        $updatedAttributes['id'] = $record->id;

        $this->assertDatabaseHas($this->table, $originalAttributes);
        $this->assertDatabaseMissing($this->table, $updatedAttributes);

        $this->repository->update($record, $updateDto);

        $this->assertDatabaseHas($this->table, $updatedAttributes);
        $this->assertDatabaseMissing($this->table, $originalAttributes);
    }

    protected function _testDelete(array $attributes): void
    {
        $dto = $this->buildDto($attributes);
        $record = $this->repository->create($dto);
        $attributes['id'] = $record->id;

        if ($this->repository->withSoftDeletes()) {
            $attributes['deleted_at'] = null;
        }

        $this->assertDatabaseHas($this->table, $attributes);

        $this->repository->delete($record);

        $this->assertDatabaseMissing($this->table, $attributes);
    }

    protected function _testRestore(array $attributes): void
    {
        if (!$this->repository->withSoftDeletes()) {
            return;
        }

        $dto = $this->buildDto($attributes);
        $record = $this->repository->create($dto);
        $attributes['id'] = $record->id;
        $restoredAttributes = $attributes + ['deleted_at' => null];

        $this->assertDatabaseHas($this->table, $attributes);

        $this->repository->delete($record);

        $this->assertDatabaseMissing($this->table, $restoredAttributes);

        $this->repository->restore($record);

        $this->assertDatabaseHas($this->table, $restoredAttributes);
    }
}