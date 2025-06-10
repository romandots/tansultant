<?php

declare(strict_types=1);

namespace App\Components\Shift;

use App\Models\Enum\ShiftStatus;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public string $user_id;
    public ?string $branch_id = null;
    public ShiftStatus $status;
}