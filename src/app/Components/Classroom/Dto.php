<?php

declare(strict_types=1);

namespace App\Components\Classroom;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public string $branch_id;
    public ?string $color = null;
    public ?int $capacity = null;
    public ?int $number = null;
}