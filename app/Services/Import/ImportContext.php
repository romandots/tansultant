<?php

namespace App\Services\Import;

use App\Models\IdMap;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class ImportContext
{
    /**
     * Данные новой модели
     * @var array
     */
    public array $data = [];
    public ?string $newId = null;

    public function __construct(
        public readonly string $entity,
        public readonly object $old,
        public readonly ImportManager $manager,
        public readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Заблокировать строку в id_maps для этой сущности+старого ID,
     * чтобы исключить гонки при параллельных upsert
     */
    public function lock(): void
    {
        DB::table('id_maps')
            ->where('entity', $this->entity)
            ->where('old_id',  (string)$this->old->id)
            ->lockForUpdate()
            ->get();
    }

    public function mapNewId(string $newId): void
    {
        $this->newId = $newId;
        IdMap::updateOrInsert(
            [
                'entity' => $this->entity,
                'old_id' => (string)$this->old->id,
            ],
            [
                'new_id' => $this->newId,
            ]
        );
    }

    public function getErrorContext(): array
    {
        return [
            'entity' => $this->entity,
            'old_record' => (array)$this->old,
            'data' => $this->data,
            'new_id' => $this->newId,
        ];
    }
}
