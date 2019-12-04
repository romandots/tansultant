<?php
/**
 * File: ClassroomRecoverTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Classroom;
use App\Services\Permissions\ClassroomsPermissions;
use Carbon\Carbon;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class ClassroomRecoverTest
 * @package Tests\Feature\Api\Lesson
 */
class ClassroomRecoverTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'manager_api/v1/classrooms';

    /**
     * @var Classroom
     */
    private $classroom;

    /**
     * @var string
     */
    private $url;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->classroom = $this->createFakeClassroom(['deleted_at' => Carbon::now()]);
        $this->url = self::URL . '/' . $this->classroom->id . '/recover';
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

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            ClassroomsPermissions::DELETE_CLASSROOMS
        ]);

        $this->assertDatabaseMissing(Classroom::TABLE, [
            'id' => $this->classroom->id,
            'deleted_at' => null,
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url)
            ->assertOk();

        $this->assertDatabaseHas(Classroom::TABLE, [
            'id' => $this->classroom->id,
            'deleted_at' => null,
        ]);
    }
}
