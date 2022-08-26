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
        $dto->courses_count = 12;
        $dto->visits_count = 12;
        $dto->days_count = 28;
        $dto->holds_count = 0;

        return $dto;
    }

    protected function getAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'price' => $this->dto->price,
            'prolongation_price' => $this->dto->prolongation_price,
            'courses_count' => $this->dto->courses_count,
            'visits_count' => $this->dto->visits_count,
            'days_count' => $this->dto->days_count,
            'holds_count' => $this->dto->holds_count,
        ];
    }

    protected function getAlternateAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'price' => 2000,
            'prolongation_price' => 1500,
            'courses_count' => 20,
            'visits_count' => 24,
            'days_count' => 30,
            'holds_count' => 1,
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