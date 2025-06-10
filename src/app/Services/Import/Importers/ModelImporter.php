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
        $ctx->info("Импортируем...");

        // DB::transaction(function () use ($ctx) {
            // Блокируем строку в id_maps на случай параллельных upsert
            // $ctx->lock();

            // Вызываем Importer, который внутри Context будет
            // делать $ctx->mapNewId($newUuid)
            /** @var Pipeline $pipeline */
            $pipeline = app(Pipeline::class);
            try {
                $pipeline
                    ->send($ctx)
                    ->through($this->pipes())
                    ->thenReturn();
            } catch (ImportException $importException) {
                // Обогощаем исключение контекстом
                throw new ($importException::class)(
                    $ctx . ": " . $importException->getMessage(),
                    $importException->getData() ?? [] + $ctx->toArray()
                );
            }
        // });
    }

    /**
     * @return class-string<PipeInterface>[]
     */
    abstract protected function pipes(): array;
}