<?php

namespace Tests\Feature\ManagerApi;

use App\Common\BaseFacade;
use App\Common\DTO\DtoWithUser;
use App\Common\DTO\SearchFilterDto;
use App\Models\User;
use App\Services\Permissions\UserRoles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class AdminControllerTest extends TestCase
{
    protected BaseFacade $facade;
    protected string $baseRoutePrefix;
    protected string $accessToken;
    protected string $tableName;
    protected string $permissionClass;
    protected bool $usesSoftDelete = false;
    protected DtoWithUser $dto;

    /**
     * @return DtoWithUser
     */
    public function getDto(): DtoWithUser
    {
        return $this->dto;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createFakeUser();
        $this->dto = $this->createDto();
        $this->usesSoftDelete = $this->facade->usesSoftDeletes() ?? false;
        $this->tableName = $this->createRecord([])::TABLE;
    }

    abstract protected function createRecord(array $attributes): Model;

    abstract protected function createDto(): DtoWithUser;

    abstract protected function getAttributes(): array;

    abstract protected function getAlternateAttributes(): array;

    final protected function getUrl(string $methodName, array $params = []): string
    {
        $methodKey = 'admin.' . $this->baseRoutePrefix . '.' . $methodName;
        return route($methodKey, $params);
    }

    final public function index(): void
    {
        $url = $this->getUrl('index');
        $this
            ->get($url)
            ->assertOk();
    }

    final public function search(SearchFilterDto $dto): void
    {
        $url = $this->getUrl('search');
        $this
            ->get($url)
            ->assertUnauthorized();

        $this->addUserToRequests();

        $this
            ->get($url)
            ->assertForbidden();

        $permissions = [$this->permissionClass::READ];
        $this->user
            ->assignRole(UserRoles::ADMIN)
            ->givePermissionTo($permissions);
        $this->assertTrue($this->user->can($permissions));

        $this
            ->get($url)
            ->assertOk();

        $url .= '?' . \http_build_query([
                'query' => $dto->query
            ]);

        $attributes = $this->getAttributes();
        $attributes['name'] = $dto->query;
        $alternateAttributes = $this->getAlternateAttributes();
        $recordOne = $this->createRecord($attributes);
        $recordTwo = $this->createRecord($alternateAttributes);

        $this->assertEquals($recordOne->name, $dto->query);
        $this->assertNotEquals($recordTwo->name, $dto->query);

        $this
            ->get($url)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'name' => $dto->query
            ])
            ->assertOk();
    }

    final public function suggest(): void
    {
        $query = 'Тестовая строка';
        $url = $this->getUrl('suggest');

        $this
            ->get($url)
            ->assertUnauthorized();

        $this->addUserToRequests();

        $this
            ->get($url)
            ->assertForbidden();

        $permissions = [$this->permissionClass::READ];
        $this->user
            ->assignRole(UserRoles::ADMIN)
            ->givePermissionTo($permissions);
        $this->assertTrue($this->user->can($permissions));

        $this
            ->get($url)
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $url .= '?' . \http_build_query([
                'query' => $query
            ]);

        $numberOfRecords = 20;
        $records = [];
        for ($i = 0; $i < $numberOfRecords; $i++) {
            $attributes = ($i > 4) ? [] : ['name' => $query . $i];
            $records[] = $this->createRecord($attributes);
        }

        Cache::shouldReceive('get')->once()->andReturn(null);
        Cache::shouldReceive('add')->once();

        $this
            ->get($url)
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment([
                'label' => $query . '0',
                'value' => $records[0]->id,
            ])
            ->assertJsonFragment([
                'label' => $query . '1',
                'value' => $records[1]->id,
            ])
            ->assertJsonFragment([
                'label' => $query . '2',
                'value' => $records[2]->id,
            ])
            ->assertJsonFragment([
                'label' => $query . '3',
                'value' => $records[3]->id,
            ])
            ->assertJsonFragment([
                'label' => $query . '4',
                'value' => $records[4]->id,
            ]);
    }

    final public function store(): void
    {
        $attributes = $this->getAttributes();
        $url = $this->getUrl('store');

        $this->assertDatabaseMissing($this->tableName, $attributes);

        $this
            ->post($url)
            ->assertUnauthorized();

        $this->addUserToRequests();

        $this
            ->post($url)
            ->assertForbidden();

        $permissions = [$this->permissionClass::CREATE];
        $this->user
            ->assignRole(UserRoles::ADMIN)
            ->givePermissionTo($permissions);
        $this->assertTrue($this->user->can($permissions));

        $this
            ->post($url)
            ->assertStatus(422);

        $this
            ->post($url, $attributes)
            ->assertCreated();

        $this->assertDatabaseHas($this->tableName, $attributes);
    }

    final public function update(): void
    {
        $oldAttributes = $this->getAttributes();
        $newAttributes = $this->getAlternateAttributes();
        $record = $this->createRecord($oldAttributes);
        $url = $this->getUrl('update', ['id' => $record->id]);

        $this
            ->put($url)
            ->assertUnauthorized();

        $this->addUserToRequests();

        $this
            ->put($url)
            ->assertForbidden();

        $permissions = [$this->permissionClass::UPDATE];
        $this->user
            ->assignRole(UserRoles::ADMIN)
            ->givePermissionTo($permissions);
        $this->assertTrue($this->user->can($permissions));

        $this
            ->put($url)
            ->assertStatus(422);

        $this->assertDatabaseHas($this->tableName, $record->toArray());
        $this->assertDatabaseMissing($this->tableName, $newAttributes);

        $this
            ->put($url, $newAttributes)
            ->assertOk();

        $this->assertDatabaseMissing($this->tableName, $record->toArray());
        $this->assertDatabaseHas($this->tableName, $newAttributes);
    }

    final public function destroy(): void
    {
        $attributes = $this->getAttributes();
        $record = $this->createRecord($attributes);
        $url = $this->getUrl('destroy', ['id' => $record->id]);

        $this
            ->delete($url)
            ->assertUnauthorized();

        $this->addUserToRequests();

        $this
            ->delete($url)
            ->assertForbidden();

        $permissions = [$this->permissionClass::DELETE];
        $this->user
            ->assignRole(UserRoles::ADMIN)
            ->givePermissionTo($permissions);
        $this->assertTrue($this->user->can($permissions));

        $attributes = ['id' => $record->id];
        if ($this->usesSoftDelete) {
            $attributes['deleted_at'] = null;
        }

        $this->assertDatabaseHas($this->tableName, $attributes);

        $this
            ->delete($url)
            ->assertOk();

        $this->assertDatabaseMissing($this->tableName, $attributes);

        $this
            ->delete($url)
            ->assertNotFound();
    }

    public function restore(): void
    {
        if (!$this->usesSoftDelete) {
            return;
        }

        $attributes = $this->getAttributes();
        $record = $this->createRecord($attributes + ['deleted_at' => Carbon::now()]);
        $url = $this->getUrl('restore', ['id' => $record->id]);

        $this
            ->post($url)
            ->assertUnauthorized();

        $this->addUserToRequests();

        $this
            ->post($url)
            ->assertForbidden();

        $permissions = [$this->permissionClass::DELETE];
        $this->user
            ->assignRole(UserRoles::ADMIN)
            ->givePermissionTo($permissions);
        $this->assertTrue($this->user->can($permissions));

        $attributes = ['id' => $record->id];
        if ($this->usesSoftDelete) {
            $attributes['deleted_at'] = null;
        }

        $this->assertDatabaseMissing($this->tableName, $attributes);

        $this
            ->post($url)
            ->assertOk();

        $this->assertDatabaseHas($this->tableName, $attributes);
    }

    protected function authorized(): self
    {
        return $this->withHeaders([
            'Authorize' => 'Bearer ' . $this->accessToken,
        ]);
    }

    protected function addUserToRequests(?User $user = null): void
    {
        Sanctum::actingAs($user ?? $this->user, ['*']);
    }
}