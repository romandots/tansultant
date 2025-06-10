<?php

namespace Tests\Feature\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Components\Classroom\Dto;
use App\Components\Loader;
use App\Http\Requests\ManagerApi\DTO\SearchClassroomsFilterDto;
use App\Models\Branch;
use App\Models\Classroom;
use App\Services\Permissions\ClassroomsPermission;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Dto $dto
 */
class ClassroomControllerTest extends AdminControllerTest
{
    private Branch $secondBranch;

    public function setUp(): void
    {
        $this->facade = Loader::classrooms();
        $this->baseRoutePrefix = 'classrooms';
        $this->permissionClass = ClassroomsPermission::class;
        parent::setUp();

        $this->secondBranch = $this->createFakeBranch();
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
        return [
            'name' => 'Другой зал',
            'branch_id' => $this->secondBranch->id,
            'number' => 2,
            'capacity' => 20,
            'color' => 'white',
        ];
    }

    public function testSearch(): void
    {
        $dto = new SearchClassroomsFilterDto();
        $dto->query = 'Тестовый зал';
        $dto->branch_id = $this->dto->branch_id;

        // Check access and all the basic stuff
        $this->search($dto);

        // Then check custom search params
        $url = $this->getUrl('search') . '?' . \http_build_query([
            'branch_id' => $dto->branch_id,
        ]);

        $this
            ->get($url)
            ->assertOk();

        $this->createFakeClassroom([
            'branch_id' => $dto->branch_id,
        ]);

        $this->createFakeClassroom([
            'branch_id' => $this->secondBranch->id,
        ]);

        $this->createFakeClassroom([
            'branch_id' => $this->secondBranch->id,
        ]);

        $this
            ->get($url)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'branch_id' => $dto->branch_id,
            ])
            ->assertOk();
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