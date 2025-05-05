<?php

namespace App\Services\Import\Pipes;

use App\Models\Model;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Closure;

class PersistEntity implements PipeInterface
{
    /**
     * @param ImportContext $ctx
     * @param Closure $next
     * @return ImportContext
     * @throws \App\Services\Import\Exceptions\ImportException
     */
    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        /** @var class-string<\Illuminate\Database\Eloquent\Model> $modelClass */
        $modelClass = $ctx->manager->mapKeyToModel($ctx->entity);
        try {
            /** @var Model $new */
            $new = $modelClass::create($ctx->data);
        } catch (\Throwable $throwable) {
            throw new ImportException("Возникла ошибка при сохранении записи", $ctx->getErrorContext());
        }
        $ctx->mapNewId($new->id);

        $ctx->logger->debug("Сохранили новую запись сущности {$ctx->entity} с ID #{$ctx->newId}");
        return $next($ctx);
    }
}
