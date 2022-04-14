<?php
/**
 * File: ClassroomDestroyTest.php
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
 * Class ClassroomDestroyTest
 * @package Tests\Feature\Api\Lesson
 */
class ClassroomDestroyTest extends TestCase
{
    use CreatesFakes;

    /**
     * @var Classroom
     */
    private Classroom $classroom;

    /**
     * @var string
     */
    private string $url;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->classroom = $this->createFakeClassroom();
        $this->url = 'admin/classrooms/' . $this->classroom->id;
    }

    public function testAccessDenied(): void
    {
        $this
            ->delete($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->delete($this->url)
            ->assertStatus(403);
    }

    public function testSuccess(): void
    {
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser([], [
            ClassroomsPermissions::DELETE
        ]);

        $this->assertDatabaseHas(Classroom::TABLE, [
            'id' => $this->classroom->id,
            'deleted_at' => null,
        ]);

        $this
            ->actingAs($user, 'api')
            ->delete($this->url)
            ->assertOk();

        $this->assertDatabaseMissing(Classroom::TABLE, [
            'id' => $this->classroom->id,
            'deleted_at' => null,
        ]);
    }
}
