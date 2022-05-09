<?php

namespace App\Common\DTO;

class DtoWithUser implements \App\Common\Contracts\DtoWithUser
{
    public ?\App\Models\User $user;

    public function __construct(?\App\Models\User $user = null)
    {
        $this->user = $user;
    }

    public function getUser(): ?\App\Models\User
    {
        return $this->user;
    }
}