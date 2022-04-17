<?php
/**
 * File: ClassroomstoreTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Classroom;
use App\Services\Permissions\ClassroomsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class ClassroomStoreTest
 * @package Tests\Feature\Api\Lesson
 */
class ClassroomStoreTest extends TestCase
{
    use CreatesFakes;

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'name',
            'branch_id',
            'color',
            'capacity',
            'number',
            'created_at',
        ]
    ];

    /**
     * @var string
     */
    private string $url;

    /**
     * @var string
     */
    private string $branchId;

    public function setUp(): void
    {
        parent::setUp();
        $this->branchId = $this->createFakeBranch()->id;
        $this->url = 'admin/classrooms';
    }

    public function testAccessDenied(): void
    {
        $this
            ->post($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->post($this->url)
            ->assertStatus(403);
    }

    public function testValidationErrors(): void
    {
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser([], [
            ClassroomsPermissions::CREATE
        ]);

        $data = [
            [
                'name' => 'Зал А',
                'branch_id' => \uuid(),
            ],
            [
                'name' => 'Зал А',
            ],
            [
                'branch_id' => $this->branchId,
            ],
            [
                'name' => 'Зал А',
                'branch_id' => $this->branchId,
                'number' => 'one',
            ],
            [
                'name' => 'Зал А',
                'branch_id' => $this->branchId,
                'capacity' => 'много',
            ]
        ];

        foreach ($data as $params) {
            $this
                ->actingAs($user, 'api')
                ->post($this->url, $params)
                ->assertStatus(422);
        }
    }

    public function testSuccess(): void
    {
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser([], [
            ClassroomsPermissions::CREATE
        ]);

        $data = [
            'name' => 'Зал А',
            'branch_id' => $this->branchId,
            'color' => '#ffffff',
            'capacity' => 30,
            'number' => 1,
        ];

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => $data
            ]);

        $this->assertDatabaseHas(Classroom::TABLE, [
            'name' => 'Зал А',
            'branch_id' => $this->branchId,
            'color' => '#ffffff',
            'capacity' => 30,
            'number' => 1,
        ]);
    }
}
