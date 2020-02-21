<?php
/**
 * File: ClassroomShowTest.php
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
 * Class ClassroomShowTest
 * @package Tests\Feature\Api\Lesson
 */
class ClassroomShowTest extends TestCase
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
     * @var Classroom
     */
    private Classroom $classroom;

    /**
     * @var string
     */
    private string $url;

    public function setUp(): void
    {
        parent::setUp();

        $this->classroom = $this->createFakeClassroom();
        $this->url = 'admin/branches/' . $this->classroom->branch_id . '/classrooms/' . $this->classroom->id;
    }

    public function testAccessDenied(): void
    {
        $this
            ->get($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->get($this->url)
            ->assertStatus(403);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            ClassroomsPermissions::READ
        ]);

        $responseData = $this->classroom->toArray();
        unset($responseData['created_at'], $responseData['updated_at'], $responseData['deleted_at']);

        $this
            ->actingAs($user, 'api')
            ->get($this->url)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => $responseData
            ]);
    }
}
