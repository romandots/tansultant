<?php

declare(strict_types=1);

namespace App\Components\Formula;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public ?string $equation = null;
}