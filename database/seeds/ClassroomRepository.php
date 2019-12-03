<?php
/**
 * File: ClassroomRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\PublicApi\DTO\Classroom as ClassroomDto;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ClassroomRepository
 */
class ClassroomRepository
{
    /**
     * @param int $id
     * @return Classroom
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): Classroom
    {
        return Classroom::query()
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->firstOrFail();
    }

    /**
     * @param ClassroomDto $dto
     * @return Classroom
     */
    public function create(ClassroomDto $dto): Classroom
    {
        $classroom = new Classroom;
        $this->fill($dto, $classroom);
        $classroom->save();

        return $classroom;
    }

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Classroom::query()
            ->whereNull('deleted_at')
            ->with('branch')
            ->get();
    }

    /**
     * @param int $branchId
     * @return Collection
     */
    public function getByBranchId(int $branchId): Collection
    {
        return Classroom::query()
            ->whereNull('deleted_at')
            ->where('branch_id', $branchId)
            ->with('branch')
            ->get();
    }

    /**
     * @param Classroom $classroom
     * @param ClassroomDto $dto
     */
    public function update(Classroom $classroom, ClassroomDto $dto): void
    {
        $this->fill($dto, $classroom);
        $classroom->save();
    }

    /**
     * @param ClassroomDto $dto
     * @param Classroom $classroom
     */
    private function fill(ClassroomDto $dto, Classroom $classroom): void
    {
        $classroom->name = $dto->name;
        $classroom->branch_id = $dto->branch_id;
        $classroom->color = $dto->color;
        $classroom->capacity = $dto->capacity;
        $classroom->number = $dto->number;
    }

    /**
     * @param Classroom $classroom
     * @throws \Exception
     */
    public function delete(Classroom $classroom): void
    {
        $classroom->delete();
    }
}
