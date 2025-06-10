<?php

declare(strict_types=1);

namespace App\Components\Account;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id = null;
    public ?string $name = null;
    public string $branch_id;
    public ?string $external_id = null;
    public ?string $external_system = null;
}