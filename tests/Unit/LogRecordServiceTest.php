<?php
/**
 * File: LogRecordServiceTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-10
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace Tests\Services\LogRecord;

use App\Models\Course;
use App\Models\LogRecord;
use App\Models\User;
use App\Services\LogRecord\LogRecordService;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

class LogRecordServiceTest extends TestCase
{
    use CreatesFakes;

    private LogRecordService $service;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->get(LogRecordService::class);
        $this->user = $this->createFakeUser([
            'name' => 'Roman Dots'
        ]);
    }

    public function testLogForCourse(): void
    {
        $course = $this->createFakeCourse([
            'name' => 'Dancehall'
        ]);

        $this->service->logCreate($this->user, $course);
        $this->assertDatabaseHas(LogRecord::TABLE, [
            'object_type' => Course::class,
            'object_id' => $course->id,
            'user_id' => $this->user->id,
            'message' => 'Roman Dots создаёт класс Dancehall'
        ]);
    }

}
