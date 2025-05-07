<?php

namespace App\Services\Import\Pipes;

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
        $service = $ctx->manager->service($ctx->entity);
        try {
            $new = $service->create($ctx->dto);
        } catch (\Throwable $throwable) {
            throw new ImportException("Ошибка сохранения записи: {$throwable->getMessage()}", $ctx->toArray());
        }

        if (!assert($new instanceof $modelClass)) {
            throw new ImportException("Модель {$ctx->entity} не является экземпляром {$modelClass}", $ctx->toArray());
        }

        $ctx->mapNewId($new->id);

        $ctx->debug("Импорт успешно завершён → #{$ctx->newId}");
        return $next($ctx);
    }
}
