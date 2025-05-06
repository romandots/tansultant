<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Contracts\ImporterInterface;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Illuminate\Pipeline\Pipeline;

abstract class ModelImporter implements ImporterInterface
{

    public function import(ImportContext $ctx): void
    {
        $ctx->logger->debug("Запускаем импорт сущности {$ctx->entity}#{$ctx->old?->id}");

        /** @var Pipeline $pipeline */
        $pipeline = app(Pipeline::class);

        try {
            $pipeline
                ->send($ctx)
                ->through([
                    ...$this->pipes(),
                    \App\Services\Import\Pipes\PersistEntity::class,
                ])
                ->thenReturn();
        } catch (ImportException $importException) {
            $ctx->logger->error("Ошибка импорта {$ctx->entity}#{$ctx->old?->id}: " . $importException->getMessage(), $importException->getData() ?? [] + $ctx->getErrorContext());
            throw $importException;
        }
    }

    /**
     * @return class-string<PipeInterface>[]
     */
    abstract protected function pipes(): array;
}