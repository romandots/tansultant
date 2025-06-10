<?php

namespace Tests\Feature\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Common\DTO\SearchFilterDto;
use App\Components\Account\Dto;
use App\Components\Loader;
use App\Models\Account;
use App\Models\Enum\AccountOwnerType;
use App\Models\Enum\AccountType;
use App\Services\Permissions\AccountsPermission;
use Illuminate\Database\Eloquent\Model;

class AccountControllerTest extends AdminControllerTest
{

    public function setUp(): void
    {
        $this->facade = Loader::accounts();
        $this->baseRoutePrefix = 'accounts';
        $this->permissionClass = AccountsPermission::class;
        parent::setUp();
    }

    /**
     * @param array $attributes
     * @return Account
     */
    protected function createRecord(array $attributes): Model
    {
        return $this->createFakeAccount($attributes);
    }

    /**
     * @return Dto
     */
    protected function createDto(): DtoWithUser
    {
        $dto = new Dto();
        $dto->owner_type = AccountOwnerType::BRANCH;
        $dto->owner_id = $this->createFakeBranch()->id;
        $dto->type = AccountType::SAVINGS;

        return $dto;
    }

    protected function getAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'owner_id' => $this->dto->owner_id,
            'owner_type' => $this->dto->owner_type->value,
            'type' => $this->dto->type->value,
        ];
    }

    protected function getAlternateAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'owner_id' => $this->dto->owner_id,
            'owner_type' => AccountOwnerType::STUDENT->value,
            'type' => AccountType::OPERATIONAL->value,
        ];
    }

    public function testSearch(): void
    {
        $dto = new SearchFilterDto();
        $dto->query = 'Тестовый счет';
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