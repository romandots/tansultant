<?php
/**
 * File: BranchestoreTest.php
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
 * Class BranchStoreTest
 * @package Tests\Feature\Api\Lesson
 */
class BranchStoreTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'manager_api/v1/branches';

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
            BranchesPermissions::CREATE_BRANCHES
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            BranchesPermissions::CREATE_BRANCHES
        ]);

        $data = [
            'name' => 'Студия Плаза',
            'summary' => 'Описание студии Плаза',
            'description' => 'Полное описание студии Плаза',
            'phone' => '8-906-432-77-66',
            'email' => 'mail@bezpravil.net',
            'url' => 'https://bezpravil.net',
            'vk_url' => 'https://vk.com/bezpravildance',
            'facebook_url' => 'https://facebook.com/bezpravil',
            'telegram_username' => 'bezpravil_bot',
            'instagram_username' => 'bezpravildance',
            'address' => [
                'country' => 'Россия',
                'city' => 'Краснодар',
                'street' => 'ул. Сормовская',
                'building' => '1/7',
                'coordinates' => [45.0234394, 38.0023121],
            ],
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

        $this->assertDatabaseHas(Branch::TABLE, [
            'name' => 'Студия Плаза',
            'summary' => 'Описание студии Плаза',
            'description' => 'Полное описание студии Плаза',
            'phone' => '8-906-432-77-66',
            'email' => 'mail@bezpravil.net',
            'url' => 'https://bezpravil.net',
            'vk_url' => 'https://vk.com/bezpravildance',
            'facebook_url' => 'https://facebook.com/bezpravil',
            'telegram_username' => 'bezpravil_bot',
            'instagram_username' => 'bezpravildance',
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
