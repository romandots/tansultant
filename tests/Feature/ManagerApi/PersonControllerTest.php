<?php

namespace Tests\Feature\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Common\DTO\SearchFilterDto;
use App\Components\Loader;
use App\Components\Person\Dto;
use App\Models\Enum\Gender;
use App\Models\Person;
use App\Services\Permissions\PersonsPermission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PersonControllerTest extends AdminControllerTest
{
    public function setUp(): void
    {
        $this->facade = Loader::people();
        $this->baseRoutePrefix = 'people';
        $this->permissionClass = PersonsPermission::class;
        $this->nameAttribute = 'last_name';
        parent::setUp();
    }

    /**
     * @param array $attributes
     * @return Person
     */
    protected function createRecord(array $attributes): Model
    {
        return $this->createFakePerson($attributes);
    }

    /**
     * @return Dto
     */
    protected function createDto(): DtoWithUser
    {
        $dto = new Dto();
        $dto->last_name = 'Dots';
        $dto->first_name = 'Roman';
        $dto->patronymic_name = 'A';
        $dto->birth_date = Carbon::parse('1986-01-08');
        $dto->phone = '+7-999-633-97-76';
        $dto->email = 'roman.dots@gmail.com';
        $dto->gender = Gender::MALE;

        return $dto;
    }

    protected function getAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'last_name' => $this->dto?->last_name,
            'first_name' => $this->dto?->first_name,
            'patronymic_name' => $this->dto?->patronymic_name,
            'birth_date' => $this->dto?->birth_date->format('Y-m-d'),
            'phone' => $this->dto?->phone,
            'email' => $this->dto?->email,
            'gender' => $this->dto->gender->value,
        ];
    }

    protected function getAlternateAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'last_name' => 'Doe',
            'first_name' => 'John',
            'patronymic_name' => 'B',
            'birth_date' => '2000-01-01',
            'phone' => '+7-900-123-12-12',
            'email' => 'john.b.doe@gmail.com',
            'gender' => Gender::FEMALE->value,
        ];
    }

    public function testSearch(): void
    {
        $dto = new SearchFilterDto();
        $dto->query = 'Тестовый чел';
        $this->search($dto);
    }

    public function testSuggest(): void
    {
        $this->suggest();
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