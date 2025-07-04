<?php

namespace Tests\Unit\Repository;

use App\Components\Classroom\Dto;
use App\Components\Loader;
use App\Models\Classroom;

final class ClassroomRepositoryTest extends RepositoryTest
{
    protected array $attributes1;
    protected array $attributes2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->table = Classroom::TABLE;
        $this->repository = Loader::classrooms()->getRepository();
        $this->dtoClass = Dto::class;

        $this->attributes1 = [
            'name' => $this->faker->word,
            'branch_id' => $this->createFakeBranch()->id,
            'number' => 123,
        ];
        $this->attributes2 = [
            'name' => $this->faker->word,
            'branch_id' => $this->createFakeBranch()->id,
            'number' => 321,
        ];
    }

    public function testCreate(): void
    {
        $this->_testCreate($this->attributes1);
    }

    public function testUpdate(): void
    {
        $this->_testUpdate($this->attributes1, $this->attributes2);
    }

    public function testDelete(): void
    {
        $this->_testDelete($this->attributes1);
    }

    public function testRestore(): void
    {
        $this->_testRestore($this->attributes1);
    }
}