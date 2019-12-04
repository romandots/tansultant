<?php
/**
 * File: IntentDestroyTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Intent;
use App\Services\Permissions\IntentsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class LessonDestroyTest
 * @package Tests\Feature\Api\Lesson
 */
class IntentDestroyTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'manager_api/v1/intents';

    /**
     * @var Intent
     */
    private $intent;

    /**
     * @var string
     */
    private $url;

    public function setUp(): void
    {
        parent::setUp();
        $this->intent = $this->createFakeIntent();
        $this->url = self::URL . '/' . $this->intent->id;
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
        $user = $this->createFakeManagerUser([], [
            IntentsPermissions::DELETE
        ]);

        $this
            ->actingAs($user, 'api')
            ->delete($this->url)
            ->assertOk();

        $this->assertDatabaseMissing(Intent::TABLE, ['id' => $this->intent->id]);
    }
}
