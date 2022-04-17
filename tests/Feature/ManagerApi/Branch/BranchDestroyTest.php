<?php
/**
 * File: BranchDestroyTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Branch;
use App\Services\Permissions\BranchesPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class BranchDestroyTest
 * @package Tests\Feature\Api\Lesson
 */
class BranchDestroyTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'admin/branches';

    /**
     * @var Branch
     */
    private $branch;

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

        $this->branch = $this->createFakeBranch();
        $this->url = self::URL . '/' . $this->branch->id;
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
            BranchesPermissions::DELETE
        ]);

        $this->assertDatabaseHas(Branch::TABLE, [
            'id' => $this->branch->id,
            'deleted_at' => null,
        ]);

        $this
            ->actingAs($user, 'api')
            ->delete($this->url)
            ->assertOk();

        $this->assertDatabaseMissing(Branch::TABLE, [
            'id' => $this->branch->id,
            'deleted_at' => null,
        ]);
    }
}
