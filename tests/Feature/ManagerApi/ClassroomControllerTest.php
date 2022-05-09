<?php

namespace Tests\Feature\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Common\DTO\SearchFilterDto;
use App\Components\Classroom\Dto;
use App\Components\Loader;
use App\Models\Classroom;
use App\Services\Permissions\ClassroomsPermission;
use Illuminate\Database\Eloquent\Model;

class ClassroomControllerTest extends AdminControllerTest
{
    public function setUp(): void
    {
        $this->facade = Loader::classrooms();
        $this->baseRoutePrefix = 'classrooms';
        $this->permissionClass = ClassroomsPermission::class;
        parent::setUp();
    }

    /**
     * @param array $attributes
     * @return Classroom
     */
    protected function createRecord(array $attributes): Model
    {
        return $this->createFakeClassroom($attributes);
    }

    /**
     * @return Dto
     */
    protected function createDto(): DtoWithUser
    {
        $dto = new Dto();
        $dto->name = 'Тестовый зал';
        $dto->branch_id = $this->createFakeBranch()->id;
        $dto->number = 1;
        $dto->capacity = 10;
        $dto->color = 'black';

        return $dto;
    }

    protected function getAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'name' => $this->dto->name,
            'branch_id' => $this->dto->branch_id,
            'number' => $this->dto->number,
            'capacity' => $this->dto->capacity,
            'color' => $this->dto->color,
        ];
    }

    protected function getAlternateAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'name' => 'Другой зал',
            'branch_id' => $this->createFakeBranch()->id,
            'number' => 2,
            'capacity' => 20,
            'color' => 'white',
        ];
    }

    public function testSearch(): void
    {
        $dto = new SearchFilterDto();
        $dto->query = 'Тестовый зал';
        $this->search($dto);
    }

    public function testSuggest(): void
    {
        $labelField = fn(Classroom $classroom) => sprintf('%s (%s)', $classroom->name, $classroom->branch->name);
        $extraFields = [
            'branch' => function (Classroom $classroom) {
                return $classroom->branch->name;
            },
            'branch_id' => 'branch_id',
        ];

        $this->suggest($labelField, 'id', $extraFields);
    }

    public function testStore(): void
    {
        $this->store();
    }

    public function testUpdate(): void
    {
        $this->update();
    }

    public function testDestroy(): void
    {
        $this->destroy();
    }

    public function testRestore(): void
    {
        $this->restore();
    }
}