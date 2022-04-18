<?php

namespace App\Common\DTO;

class DtoWIthUser implements \App\Common\Contracts\DtoWithUser
{
    public \App\Models\User $user;

    public function getUser(): \App\Models\User
    {
        return $this->user;
    }
}