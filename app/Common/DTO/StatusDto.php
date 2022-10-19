<?php

namespace App\Common\DTO;

class StatusDto extends DtoWithUser
{
    public function __construct(
        public readonly \App\Models\User $user,
        public readonly string $id,
    ) {
    }
}