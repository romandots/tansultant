<?php

declare(strict_types=1);

namespace App\Components\LogRecord;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\LogRecord
 */
class Formatter extends BaseFormatter
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'action' => $this->action->value,
            'object_type' => $this->object_type->value,
            'object_id' => $this->object_id,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
