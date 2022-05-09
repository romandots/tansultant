<?php

declare(strict_types=1);

namespace App\Components\User;

use App\Models\Enum\UserStatus;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public ?string $name;
    public string $person_id;
    public string $username;
    public string $password;
    public UserStatus $status;
}