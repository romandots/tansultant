<?php

namespace App\Common\Contracts;

interface DtoWithUser
{
    public function getUser(): \App\Models\User;
}