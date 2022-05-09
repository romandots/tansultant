<?php

namespace Tests\Feature\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Common\DTO\SearchFilterDto;
use App\Components\Branch\AddressDto;
use App\Components\Branch\Dto;
use App\Components\Loader;
use App\Models\Branch;
use App\Services\Permissions\BranchesPermission;
use Illuminate\Database\Eloquent\Model;

class BranchControllerTest extends AdminControllerTest
{
    public function setUp(): void
    {
        $this->facade = Loader::branches();
        $this->baseRoutePrefix = 'branches';
        $this->permissionClass = BranchesPermission::class;
        parent::setUp();
    }

    /**
     * @param array $attributes
     * @return Branch
     */
    protected function createRecord(array $attributes): Model
    {
        return $this->createFakeBranch($attributes);
    }

    /**
     * @return Dto
     */
    protected function createDto(): DtoWithUser
    {
        $dto = new Dto();
        $dto->name = 'Some fancy branch';
        $dto->summary = 'Some fancy branch summary';
        $dto->description = 'Some fancy branch description';
        $dto->phone = '01233231233';
        $dto->email = 'some@you.know';
        $dto->url = 'https://some.fancy.domain';
        $dto->vk_url = null;
        $dto->facebook_url = null;
        $dto->telegram_username = null;
        $dto->instagram_username = null;
        $dto->number = 1;
        $dto->address = new AddressDto();
        $dto->address->country = 'Pandora';
        $dto->address->city = 'Los Donuts';
        $dto->address->street = '11 Avenue';
        $dto->address->building = '1232';
        $dto->address->coordinates = [48.213, 37.912];

        return $dto;
    }

    protected function getAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'name' => $this->dto->name,
            'summary' => $this->dto->summary,
            'description' => $this->dto->description,
            'phone' => $this->dto->phone,
            'email' => $this->dto->email,
            'url' => $this->dto->url,
            'vk_url' => $this->dto->vk_url,
            'facebook_url' => $this->dto->facebook_url,
            'telegram_username' => $this->dto->telegram_username,
            'instagram_username' => $this->dto->instagram_username,
            'number' => $this->dto->number,
//            'address' => [
//                'country' => $this->dto->address->country,
//                'city' => $this->dto->address->city,
//                'street' => $this->dto->address->street,
//                'building' => $this->dto->address->building,
//                'coordinates' => $this->dto->address->coordinates,
//            ]
        ];
    }

    protected function getAlternateAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'name' => 'Другой филиал',
            'summary' => 'Другой саммари',
            'description' => 'Другое описание',
            'phone' => '79992134141',
            'email' => 'mail@domain.com',
            'url' => 'https://google.com',
            'vk_url' => null,
            'facebook_url' => null,
            'telegram_username' => null,
            'instagram_username' => null,
            'number' => 2,
        ];
    }

    public function testSearch(): void
    {
        $dto = new SearchFilterDto();
        $dto->query = 'Тестовый филиал';
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