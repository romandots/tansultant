<?php

declare(strict_types=1);

namespace App\Components\User;

use App\Common\DTO\DtoWIthUser;

class UpdateUserPasswordDto extends DtoWIthUser
{
    public string $old_password;
    public string $new_password;
    public bool $skip_check = false;
}
