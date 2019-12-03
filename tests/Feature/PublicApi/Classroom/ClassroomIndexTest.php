<?php
/**
 * File: ClassroomIndexTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\PublicApi\Classroom;

use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class ClassroomIndexTest
 * @package Tests\Feature\PublicApi\Classroom
 */
class ClassroomIndexTest extends TestCase
{
    use CreatesFakes;

    private const URL = 'api/v1/classrooms';

    private const JSON_STRUCTURE = [
        'data' => [
            [
                'branch',
                'color',
                'capacity',
                'number',
            ]
        ]
    ];

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess(): void
    {
        $branch = $this->createFakeBranch();
        $this->createFakeClassroom(['branch_id' => $branch->id]);
        $this->createFakeClassroom(['branch_id' => $branch->id]);
        $this->createFakeClassroom(['branch_id' => $branch->id]);

        $url = self::URL;

        $this
            ->get($url)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE);
    }

    public function testFilterByBranch(): void
    {
        $branch = $this->createFakeBranch();
        $anotherBranch = $this->createFakeBranch();
        $classroom1 = $this->createFakeClassroom(['branch_id' => $branch->id]);
        $classroom2 = $this->createFakeClassroom(['branch_id' => $branch->id]);
        $classroom3 = $this->createFakeClassroom(['branch_id' => $branch->id]);
        $classroom4 = $this->createFakeClassroom(['branch_id' => $anotherBranch->id]);

        $queryString = \http_build_query([
            'branch_id' => $branch->id
        ]);
        $url = self::URL . '?' . $queryString;

        $this
            ->get($url)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    [
                        'id' => $classroom1->id,
                        'name' => $classroom1->name,
                        'branch' => [
                            'id' => $classroom1->branch_id
                        ],
                        'color' => $classroom1->color,
                        'capacity' => $classroom1->capacity,
                        'number' => $classroom1->number,
                    ],
                    [
                        'id' => $classroom2->id,
                        'name' => $classroom2->name,
                        'branch' => [
                            'id' => $classroom2->branch_id
                        ],
                        'color' => $classroom2->color,
                        'capacity' => $classroom2->capacity,
                        'number' => $classroom2->number,
                    ],
                    [
                        'id' => $classroom3->id,
                        'name' => $classroom3->name,
                        'branch' => [
                            'id' => $classroom3->branch_id
                        ],
                        'color' => $classroom3->color,
                        'capacity' => $classroom3->capacity,
                        'number' => $classroom3->number,
                    ]
                ]
            ])
            ->assertJsonMissing([
                'data' => [
                    [
                        'id' => $classroom4->id,
                        'name' => $classroom4->name,
                        'color' => $classroom4->color,
                        'capacity' => $classroom4->capacity,
                        'number' => $classroom4->number,
                    ]
                ]
            ]);
    }
}
