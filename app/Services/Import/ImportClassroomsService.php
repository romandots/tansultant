<?php

namespace App\Services\Import;

use App\Components\Loader;
use App\Models\Classroom;

class ImportClassroomsService extends ImportService
{
    use Traits\ClassroomsMapTrait;

    protected string $table = 'dancefloors';
    /** @var array<int> */
    protected array $currentNumbers = [];

    protected function askDetails(): void
    {
        $this->buildBranchesMap();
        $this->buildClassroomsMap();
    }

    protected function prepareImportQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->dbConnection
            ->table($this->table)
            ->join('studios', 'studios.id', '=', 'dancefloors.studio_id')
            ->select('dancefloors.*', 'studios.id as studio_id', 'studios.studio_title as studio_title')
            ->orderBy('id', 'asc');
    }

    protected function importRecord(\stdClass $record): void
    {
        $tag = '#' . $record->id . ' (' . $record->name . ', ' . $record->studio_title . ')';
        $branchId = $this->mappedToBranch($record->studio_id);

        if ($branchId === null) {
            $this->skipped($tag, 'Branch not mapped');
            return;
        }

        if ($this->mappedToClassroom($record->id)) {
            $this->skipped($tag, 'Already exists and mapped');
            return;
        }

        $classroom = $this->createClassroom($record, $branchId);
        $this->imported($classroom->id);
    }

    protected function createClassroom(\stdClass $record, string $branchId): Classroom
    {
        if (!isset($this->currentNumbers[$branchId])) {
            $this->currentNumbers[$branchId] = 0;
        }
        $classroom = new Classroom([
            'id' => \uuid(),
            'name' => $record->name,
            'branch_id' => $branchId,
            'color' => $record->color,
            'capacity' => null,
            'number' => $this->currentNumbers[$branchId]++,
        ]);

        Loader::classrooms()->getRepository()->save($classroom);

        return $classroom;
    }

}