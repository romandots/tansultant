<?php

namespace App\Services\Import\Traits;

use App\Components\Loader;
use Illuminate\Support\Collection;

trait BranchesMapTrait
{
    protected array $branchesMap = [];
    protected Collection $studios;
    protected Collection $branches;

    protected function buildBranchesMap(): void
    {
        $this->studios = $this->dbConnection
            ->table('studios')
            ->get(['id', 'studio_title']);

        $this->branches = Loader::branches()->getAll();

        $branchesKeys = $this->branches->pluck('id', 'name')->toArray();
        $branchesValues = $this->branches->pluck('name')->toArray();
        foreach ($this->studios as $studio) {
            if (count($branchesValues) < 1) {
                break;
            }

            $pickedValue = $this->cli->choice(
                "Which branch should be associated with studio {$studio->studio_title}",
                $branchesValues + [count($branchesValues) => '--']
            );
            $pickedId = $branchesKeys[$pickedValue] ?? null;

            if ($pickedId === null) {
                continue;
            }

            $this->branchesMap[$studio->id] = $pickedId;
            $pickedKey = array_search($pickedValue, $branchesValues, true);
            unset($branchesValues[$pickedKey], $branchesKeys[$pickedValue]);
        }
    }

    protected function mappedToBranch(int $studioId): ?string
    {
        return $this->branchesMap[$studioId] ?? null;
    }
}