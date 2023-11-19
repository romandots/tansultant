<?php

namespace App\Services\Import;

use App\Components\Loader;
use App\Models\Classroom;
use App\Services\Import\Maps\BranchesMap;
use App\Services\Import\Maps\ClassroomsMap;
use App\Services\Import\Maps\ObjectsMap;
use Illuminate\Database\Eloquent\Model;

class ImportClassroomsService extends ImportService
{
    protected string $table = 'dancefloors';
    protected ObjectsMap $branchesMapper;
    protected string $mapClass = ClassroomsMap::class;

    public function __construct(\Illuminate\Console\Command $cli)
    {
        parent::__construct($cli);
        $this->branchesMapper = $this->getMapper(BranchesMap::class);
        if ($this->branchesMapper->isMapEmpty()) {
            $this->branchesMapper->buildMap();
        }
    }

    protected function getTag(\stdClass $record): string
    {
        return '#' . $record->id . ' (' . $record->name . ', ' . $record->studio_title . ')';
    }

    protected function askDetails(): void
    {
        $this->buildMap();
    }

    protected function prepareImportQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->dbConnection
            ->table($this->table)
            ->join('studios', 'studios.id', '=', 'dancefloors.studio_id')
            ->select('dancefloors.*', 'studios.id as studio_id', 'studios.studio_title as studio_title')
            ->orderBy('id', 'asc');
    }

    protected function validateImport(\stdClass $record): void
    {
        $isBranchMapped = (bool)$this->getMapper()->getBranchesMapper()->mapped($record->studio_id);
        if ($isBranchMapped === false) {
            throw new Exceptions\ImportServiceException('Branch is not mapped');
        }

        parent::validateImport($record);
    }

    protected function processImportRecord(\stdClass $record): Model
    {
        $branchId =  $this->getMapper()->getBranchesMapper()->mapped($record->studio_id);
        $classroom = $this->createClassroom($record, $branchId);
        Loader::classrooms()->getRepository()->save($classroom);

        return $classroom;
    }

    private function createClassroom(\stdClass $record, string $branchId): Classroom
    {
        return new Classroom([
            'id' => \uuid(),
            'name' => $record->name,
            'branch_id' => $branchId,
            'color' => $record->color,
            'capacity' => null,
            'number' => $this->nextNumber(),
        ]);
    }
}