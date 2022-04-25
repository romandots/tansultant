<?php

namespace App\Common\Contracts;

use App\Models\User;

interface DtoWithUser
{
    public function getUser(): ?User;
}