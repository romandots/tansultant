<?php
/**
 * File: BranchShowTest.php
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
 * Class BranchShowTest
 * @package Tests\Feature\Api\Lesson
 */
class BranchShowTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'admin/branches';

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'name',
            'summary',
            'description',
            'phone',
            'email',
            'url',
            'vk_url',
            'facebook_url',
            'telegram_username',
            'instagram_username',
            'address',
            'number',
        ]
    ];

    /**
     * @var Branch
     */
    private $branch;

    /**
     * @var string
     */
    private $url;

    public function setUp(): void
    {
        parent::setUp();

        $this->branch = $this->createFakeBranch();
        $this->url = self::URL . '/' . $this->branch->id;
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
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser([], [
            BranchesPermissions::READ
        ]);

        $responseData = $this->branch->toArray();
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
