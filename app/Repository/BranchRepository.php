<?php
/**
 * File: BranchRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BranchRepository
 * @package App\Repository
 */
class BranchRepository
{
    /**
     * @return Collection|Branch[]
     */
    public function getAll(): Collection
    {
        return Branch::query()
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Branch
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): ?Branch
    {
        return Branch::query()
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->firstOrFail();
    }

    /**
     * @param \App\Http\Requests\Api\DTO\Branch $dto
     * @return Branch
     * @throws \Exception
     */
    public function create(\App\Http\Requests\Api\DTO\Branch $dto): Branch
    {
        $branch = new Branch;
        $branch->id = \uuid();
        $branch->name = $dto->name;
        $branch->save();

        return $branch;
    }
}
