<?php

namespace App\Services\Import;

use App\Components\Loader;
use App\Models\Branch;
use Carbon\Carbon;

class ImportBranchesService extends ImportService
{
    use Traits\BranchesMapTrait;

    protected string $table = 'studios';
    protected int $currentNumber = 0;

    protected function askDetails(): void
    {
        $this->buildBranchesMap();
        $this->currentNumber = count($this->branchesMap);
    }

    protected function prepareImportQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->dbConnection
            ->table($this->table)
            ->orderBy('id', 'asc');
    }

    protected function importRecord(\stdClass $record): void
    {
        $tag = '#' . $record->id . ' (' . $record->studio_title . ')';
        if ($this->mappedToBranch($record->id)) {
            $this->skipped($tag, 'Already exists and mapped');
            return;
        }

        $branch = $this->createBranch($record);
        $this->imported($branch->id);
    }


    protected function createBranch(\stdClass $record): Branch
    {
        $branch = new Branch([
            'id' => \uuid(),
            'name' => $record->studio_title,
            'summary' => null,
            'description' => $record->description,
            'phone' => $record->phone,
            'email' =>  null,
            'number' => $this->currentNumber++,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        Loader::branches()->getRepository()->save($branch);

        return $branch;
    }
}