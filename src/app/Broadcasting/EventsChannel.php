<?php

namespace App\Broadcasting;

use App\Models\Enum\LogRecordObjectType;

class EventsChannel
{
    public function join(\App\Models\User $user, LogRecordObjectType $type, string $id): bool
    {
//        $model = $type->getClass();
//        $record = $model::find($id);

        return true;
    }
}