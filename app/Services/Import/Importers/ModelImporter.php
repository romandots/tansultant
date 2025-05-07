<?php

namespace App\Services\Import\Importers;

use App\Services\Import\Contracts\ImporterInterface;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Illuminate\Pipeline\Pipeline;

abstract class ModelImporter implements ImporterInterface
{

    /**
     * @param ImportContext $ctx
     * @return void
     * @trhrows ImportException
     */
    public function import(ImportContext $ctx): void
    {
        $ctx->logger->info("Импорт сущности {$ctx->entity}#{$ctx->old?->id}");

        /** @var Pipeline $pipeline */
        $pipeline = app(Pipeline::class);

        try {
            $pipeline
                ->send($ctx)
                ->through($this->pipes())
                ->thenReturn();
        } catch (ImportException $importException) {
            // Обогощаем исключение контекстом
            throw new ($importException::class)($importException->getMessage(), $importException->getData() ?? [] + $ctx->getErrorContext());
        }
    }

    /**
     * @return class-string<PipeInterface>[]
     */
    abstract protected function pipes(): array;
}