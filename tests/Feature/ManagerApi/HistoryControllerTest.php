<?php

namespace Tests\Feature\ManagerApi;

use App\Components\Loader;
use App\Events\User\UserCreatedEvent;
use App\Models\Enum\LogRecordAction;
use App\Models\Enum\LogRecordObjectType;
use App\Models\Enum\UserStatus;
use App\Services\Permissions\UserRoles;
use Carbon\Carbon;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $dropTypes = true;

    private \App\Models\User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(RolesTableSeeder::class);
        $this->user = $this->createFakeUser();
    }

    public function testHistory(): void
    {
        $this->freezeTime(function (Carbon $carbon) {
            $logRecordObjectType = LogRecordObjectType::USER;

            $dto = new \App\Components\User\Dto($this->user);
            $dto->status = UserStatus::PENDING;
            $dto->name = 'Testing Test';
            $dto->password = '1234567';
            $dto->username = 'testing_test';
            $dto->person_id = $this->createFakePerson()->id;

            Event::fake();

            $record = Loader::users()->create($dto);

            Event::assertDispatched(UserCreatedEvent::class, function (UserCreatedEvent $event) use ($record) {
                return $event->getUserId() === $record->id;
            });

            $url = $this->getRoute($logRecordObjectType, $record->id);

            $this->get($url)
                ->assertUnauthorized();

            Sanctum::actingAs($this->user, ['*']);

            $this->get($url)
                ->assertForbidden();

            $this->user
                ->assignRole(UserRoles::ADMIN);

            $this->get($url)
                ->assertJson([
                    'data' => [
                        [
                            'message' => "{$this->user} создаёт пользователя {$record}",
                            'action' => LogRecordAction::CREATE->value,
                            'object_type' => LogRecordObjectType::USER->value,
                            'object_id' => $record->id,
                            'old_value' => null,
                            'new_value' => null,
                            'created_at' => $carbon->now()->format('Y-m-d H:i:s'),
                        ],
                    ],
                ])
                ->assertOk();
        });
    }

    protected function getRoute(LogRecordObjectType $logRecordObjectType, string $recordId): string
    {
        return \route('admin.history', [$logRecordObjectType->value, $recordId]);
    }
}