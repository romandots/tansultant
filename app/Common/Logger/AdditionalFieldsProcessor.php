<?php

namespace App\Common\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class AdditionalFieldsProcessor implements ProcessorInterface
{

    public function __invoke(LogRecord $record)
    {
        $context = $record->context;

        $context += [
            'app_version' => config('version.full_version'),
            'app_name' => config('app.name'),
            'app_environment' => config('app.env'),
        ];

        return $record->with(context: $context);
    }
}