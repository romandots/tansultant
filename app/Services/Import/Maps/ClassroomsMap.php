<?php

namespace App\Services\Import\Maps;

use App\Components\Loader;
use Illuminate\Support\Collection;

class ClassroomsMap extends ObjectsMap
{

    public function getBranchesMapper(): BranchesMap
    {
        return app()->get(BranchesMap::class)->setCli($this->cli)->setDbConnection($this->dbConnection);
    }

    protected function getPromptMessage(object $oldObject, ?string $additionalText = null): string
    {
        return "Which classroom should be associated with dancefloor {$oldObject->name}" .
            ($additionalText ? sprintf(" %s", $additionalText): '');
    }

    protected function getMapCacheKey(): string
    {
        return 'studios_to_classrooms_map';
    }

    public function getNewObjects(): Collection
    {
        return Loader::classrooms()->getAll();
    }

    public function getOldObjects(): Collection
    {
        return $this->dbConnection
            ->table('dancefloors')
            ->orderBy('studio_id')
            ->orderBy('id')
            ->get(['id', 'name', 'studio_id']);
    }

    public function buildMap(): void
    {
        $this->oldObjects = $this->getOldObjects();
        $this->newObjects = $this->getNewObjects();
        $branchesMap = $this->getBranchesMapper()->getMap();

        foreach ($branchesMap as $studioId => $branchId) {
            $dancefloorsByStudio = $this->oldObjects->where('studio_id', $studioId);
            $classroomsByBranch = Loader::classrooms()->getAll()->where('branch_id', $branchId);
            $classroomsKeys = $classroomsByBranch
                ->mapWithKeys(
                    fn($classroom) => [sprintf('%s (%s)', $classroom->name, $classroom->branch->name) => $classroom->id]
                )
                ->toArray();
            $classroomsValues = array_values(
                $classroomsByBranch
                    ->map(fn($record) => sprintf('%s (%s)', $record->name, $record->branch->name))
                    ->toArray()
            );
            $classroomsValues[] = '--';

            $studio = $this->getBranchesMapper()->getOldObjects()->where('id', $studioId)->first();
            $studioName = $studio?->studio_title;

            foreach ($dancefloorsByStudio as $dancefloor) {
                if (count($classroomsValues) === 1) {
                    break;
                }

                $pickedValue = $this->cli->choice(
                    $this->getPromptMessage($dancefloor, $studioName),
                    $classroomsValues,
                );
                $pickedId = $classroomsKeys[$pickedValue] ?? null;

                if ($pickedId === null) {
                    continue;
                }

                $this->map($dancefloor->id, $pickedId);
                $pickedKey = array_search($pickedValue, $classroomsValues, true);
                unset($classroomsValues[$pickedKey], $classroomsKeys[$pickedValue]);
            }
        }
    }
}