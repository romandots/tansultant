<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Models\Enum\AccountOwnerType;
use App\Models\Enum\AccountType;

class Dto implements \App\Common\Contracts\Dto
{
    public ?string $id = null;
    public string $name;
    public AccountType $type;
    public AccountOwnerType $owner_type;
    public string $owner_id;
}