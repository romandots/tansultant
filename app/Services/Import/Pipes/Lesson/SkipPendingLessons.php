<?php

namespace App\Services\Import\Pipes\Lesson;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportSkippedException;
use App\Services\Import\ImportContext;
use App\Services\Import\Traits\DatesTrait;
use Closure;

class SkipPendingLessons implements PipeInterface
{
    use DatesTrait;

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        if ($this->getLessonEndTimestamp($ctx->old) > time()) {
            throw new ImportSkippedException("Урок еще не прошёл");
        }

        if (empty($ctx->old->class_id)) {
            throw new ImportSkippedException("Индивидуалка или субаренда");
        }

        return $next($ctx);
    }
}