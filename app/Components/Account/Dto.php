<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Models\Enum\AccountType;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id = null;
    public ?string $name = null;
    public AccountType $type;
    public string $branch_id;
}