<?php

declare(strict_types=1);

namespace App\Components\Instructor;

use App\Models\Enum\InstructorStatus;

class Dto extends \App\Common\DTO\DtoWIthUser
{
    public ?string $id;
    public string $person_id;
    public string $name;
    public ?string $description = null;
    public InstructorStatus $status;
    public bool $display;
}