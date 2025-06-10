<?php

declare(strict_types=1);

namespace App\Components\User;

use App\Models\Enum\UserStatus;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public ?string $name = null;
    public string $person_id;
    public ?string $username = null;
    public ?string $password = null;
    public ?UserStatus $status = null;
    /**
     * @var string[]
     */
    public array $roles = [];
}