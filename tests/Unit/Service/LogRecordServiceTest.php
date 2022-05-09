<?php
/**
 * File: LogRecordServiceTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-10
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Components\Loader;
use App\Models\Enum\LogRecordAction;
use App\Models\Enum\LogRecordObjectType;
use App\Models\LogRecord;
use App\Models\User;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

class LogRecordServiceTest extends TestCase
{
    use CreatesFakes;

    protected User $user;
    protected \App\Components\LogRecord\Facade $facade;
    protected \App\Components\LogRecord\Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->facade = Loader::logRecords();
        $this->service = $this->facade->getService();
        $this->user = $this->createFakeUser([
            'name' => 'Roman Dots'
        ]);
    }

    public function testLogForCourse(): void
    {
        $course = $this->createFakeCourse([
            'name' => 'Dancehall'
        ]);

        $this->service->log($this->user, LogRecordAction::CREATE, $course);
        $this->assertDatabaseHas(LogRecord::TABLE, [
            'object_type' => LogRecordObjectType::COURSE->value,
            'object_id' => $course->id,
            'user_id' => $this->user->id,
            'message' => 'Roman Dots создаёт класс Dancehall'
        ]);
    }

}
