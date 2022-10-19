<?php

namespace App\Common\DTO;

class DtoWithUser implements \App\Common\Contracts\DtoWithUser
{
    public function __construct(
        public readonly ?\App\Models\User $user = null
    ) { }

    public function getUser(): ?\App\Models\User
    {
        return $this->user;
    }
}