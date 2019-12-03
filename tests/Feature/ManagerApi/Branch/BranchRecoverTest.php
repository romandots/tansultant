<?php
/**
 * File: BranchRecoverTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Branch;
use App\Services\Permissions\BranchesPermissions;
use Carbon\Carbon;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class BranchRecoverTest
 * @package Tests\Feature\Api\Lesson
 */
class BranchRecoverTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'manager_api/v1/branches';

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

        $this->branch = $this->createFakeBranch(['deleted_at' => Carbon::now()]);
        $this->url = self::URL . '/' . $this->branch->id . '/recover';
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
            BranchesPermissions::DELETE_BRANCHES
        ]);

        $this->assertDatabaseMissing(Branch::TABLE, [
            'id' => $this->branch->id,
            'deleted_at' => null,
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url)
            ->assertOk();

        $this->assertDatabaseHas(Branch::TABLE, [
            'id' => $this->branch->id,
            'deleted_at' => null,
        ]);
    }
}
