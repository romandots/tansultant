<?php

namespace Tests\Feature\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Common\DTO\SearchFilterDto;
use App\Components\Loader;
use App\Components\Tariff\Dto;
use App\Models\Tariff;
use App\Services\Permissions\TariffsPermission;
use Illuminate\Database\Eloquent\Model;

class TariffControllerTest extends AdminControllerTest
{

    public function setUp(): void
    {
        $this->facade = Loader::tariffs();
        $this->baseRoutePrefix = 'tariffs';
        $this->permissionClass = TariffsPermission::class;
        parent::setUp();
    }

    /**
     * @param array $attributes
     * @return Tariff
     */
    protected function createRecord(array $attributes): Model
    {
        return $this->createFakeTariff($attributes);
    }

    /**
     * @return Dto
     */
    protected function createDto(): DtoWithUser
    {
        $dto = new Dto();
        $dto->name = 'Тестовый тариф';
        $dto->price = 1200;
        $dto->prolongation_price = 1000;
        $dto->courses_limit = 12;
        $dto->visits_limit = 12;
        $dto->days_limit = 28;
        $dto->holds_limit = 0;

        return $dto;
    }

    protected function getAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'price' => $this->dto->price,
            'prolongation_price' => $this->dto->prolongation_price,
            'courses_limit' => $this->dto->courses_limit,
            'visits_limit' => $this->dto->visits_limit,
            'days_limit' => $this->dto->days_limit,
            'holds_limit' => $this->dto->holds_limit,
        ];
    }

    protected function getAlternateAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'price' => 2000,
            'prolongation_price' => 1500,
            'courses_limit' => 20,
            'visits_limit' => 24,
            'days_limit' => 30,
            'holds_limit' => 1,
        ];
    }

    public function testSearch(): void
    {
        $dto = new SearchFilterDto();
        $dto->query = 'Тестовый тариф';
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