<?php

namespace App\Services\Import\Contracts;

use App\Services\Import\ImportContext;

interface ImporterInterface
{

    /**
     * Импорт одной старой записи в новую систему.
     *
     * @param  ImportContext  $ctx
     *   • $ctx->entity  — ключ сущности (например, 'accounts')
     *   • $ctx->old     — объект с данными из старой БД
     *   • $ctx->data    — сюда собираем поля для новой модели
     *   • $ctx->manager — ImportManager, умеющий подхватить все зависимости
     *
     * @return void
     *
     * Внутри импортора:
     * 1) Маппинг $ctx->old → $ctx->data
     * 2) Вызов $ctx->manager->ensureImported(...) для всех связанных сущностей
     * 3) Сохранение новой модели и создание записи в id_maps через $ctx->mapNewId()
     */
    public function import(ImportContext $ctx): void;
}