<?php

namespace App\Services\Import\Traits;

use App\Components\Loader;
use App\Models\Classroom;
use Illuminate\Support\Collection;

trait ClassroomsMapTrait
{
    use BranchesMapTrait;

    /** @var array<Collection<\stdClass>> */
    protected array $dancefloors = [];
    /** @var array<Collection<Classroom>> */
    protected array $classrooms = [];
    protected array $classroomsMap = [];

    protected function mappedToClassroom(int $dancefloorId): ?string
    {
        return $this->classroomsMap[$dancefloorId] ?? null;
    }

    protected function buildClassroomsMap(): void
    {
        foreach ($this->branchesMap as $studioId => $branchId) {
            $this->dancefloors[$studioId] = $this->dbConnection
                ->table('dancefloors')
                ->where('studio_id', $studioId)
                ->get(['id', 'name']);
            $this->classrooms[$branchId] = Loader::classrooms()->getAll()->where('branch_id', $branchId);
            $classroomsKeys = $this->classrooms[$branchId]
                ->mapWithKeys(
                    fn($classroom) => [sprintf('%s (%s)', $classroom->name, $classroom->branch->name) => $classroom->id]
                )
                ->toArray();
            $classroomsValues = array_values(
                $this->classrooms[$branchId]
                    ->map(fn($record) => sprintf('%s (%s)', $record->name, $record->branch->name))
                    ->toArray()
            );
            $classroomsValues[] = '--';

            $studioName = $this->studios->where('id', $studioId)->first()?->studio_title;

            foreach ($this->dancefloors[$studioId] as $dancefloor) {
                if (count($classroomsValues) < 1) {
                    break;
                }

                $pickedValue = $this->cli->choice(
                    "Which classroom should be associated with dancefloor {$dancefloor->name} ({$studioName})",
                    $classroomsValues
                );
                $pickedId = $classroomsKeys[$pickedValue] ?? null;

                if ($pickedId === null) {
                    continue;
                }

                $this->classroomsMap[$dancefloor->id] = $pickedId;
                $pickedKey = array_search($pickedValue, $classroomsValues, true);
                unset($classroomsValues[$pickedKey], $classroomsKeys[$pickedValue]);
            }
        }
    }
}