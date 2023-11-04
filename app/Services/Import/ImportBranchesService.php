<?php

namespace App\Services\Import;

use App\Components\Loader;
use App\Models\Branch;
use App\Services\Import\Maps\BranchesMap;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ImportBranchesService extends ImportService
{
    protected string $table = 'studios';
    protected string $mapClass = BranchesMap::class;

    protected function askDetails(): void
    {
        $this->buildMap();
    }

    protected function prepareImportQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->dbConnection
            ->table($this->table)
            ->orderBy('id', 'asc');
    }

    protected function getTag(\stdClass $record): string
    {
        return '#' . $record->id . ' (' . $record->studio_title . ')';
    }

    protected function processImportRecord(\stdClass $record): Model
    {
        $branch = $this->createBranch($record);
        Loader::branches()->getRepository()->save($branch);

        return $branch;
    }

    protected function createBranch(\stdClass $record): Branch
    {
        return new Branch([
            'id' => \uuid(),
            'name' => $record->studio_title,
            'summary' => null,
            'description' => $record->description,
            'phone' => $record->phone,
            'email' =>  null,
            'number' => $this->nextNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}