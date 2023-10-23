<?php

namespace App\Services\Import;

use App\Components\Loader;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ImportBranchesService extends ImportService
{

    protected string $table = 'studios';
    protected Collection $studios;
    protected Collection $branches;
    protected array $map = [];
    protected int $currentNumber = 0;

    protected function fetchMapsKeys(): void
    {
        $this->studios = $this->dbConnection
            ->table('studios')
            ->get(['id', 'studio_title']);

        $this->branches = Loader::branches()->getAll();
    }

    protected function askDetails(): void
    {
        $branchesKeys = $this->branches->pluck('id', 'name')->toArray();
        $branchesValues = $this->branches->pluck('name')->toArray();
        foreach ($this->studios as $studio) {
            $pickedValue = $this->cli->choice(
                "Which branch should be associated with studio {$studio->studio_title}:",
                $branchesValues + [count($branchesValues) => '--']
            );
            $pickedId = $branchesKeys[$pickedValue] ?? null;

            if ($pickedId === null) {
                continue;
            }

            $this->map[$studio->id] = $pickedId;
            $pickedKey = array_search($pickedValue, $branchesValues, true);
            unset($branchesValues[$pickedKey], $branchesKeys[$pickedValue]);


            if (count($branchesValues) < 1) {
                break;
            }
        }
        $this->currentNumber = count($this->map);
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
        if ($this->mappedTo($record->id)) {
            $this->skipped($tag, 'Already exists and mapped');
            return;
        }

        $branch = $this->createBranch($record);
        $this->imported($branch->id);
    }

    protected function mappedTo(int $studioId): ?string
    {
        return $this->map[$studioId] ?? null;
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