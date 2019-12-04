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

    protected const URL = 'manager_api/v1/classrooms';

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
     * @var Classroom
     */
    private $classroom;

    /**
     * @var string
     */
    private $url;

    public function setUp(): void
    {
        parent::setUp();
        $this->url = self::URL;
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

    /**
     * @param array $data
     * @dataProvider provideInvalidData
     */
    public function testValidationErrors(array $data): void
    {
        $user = $this->createFakeManagerUser([], [
            ClassroomsPermissions::CREATE_CLASSROOMS
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            ClassroomsPermissions::CREATE_CLASSROOMS
        ]);

        $branchId = $this->createFakeBranch()->id;

        $data = [
            'name' => 'Зал А',
            'branch_id' => $branchId,
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
            'branch_id' => $branchId,
            'color' => '#ffffff',
            'capacity' => 30,
            'number' => 1,
        ]);
    }

    /**
     * @return array
     */
    public function provideInvalidData(): array
    {
        return [
            [
                [
                    'student_id' => 'text value',
                    'lesson_id' => 1
                ]
            ],
            [
                [
                    'student_id' => 1,
                    'lesson_id' => 'text value'
                ]
            ],
            [
                [
                    'lesson_id' => 1
                ]
            ],
            [
                [
                    'student_id' => 1
                ]
            ],
            [
                [
                    'lesson_id' => 1,
                    'student_id' => 1,
                    'promocode_id' => 'WrongType'
                ]
            ]
        ];
    }
}
