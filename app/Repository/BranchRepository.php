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

/**
 * Class BranchRepository
 * @package App\Repository
 */
class BranchRepository
{
    /**
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Branch
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): ?Branch
    {
        return Branch::query()
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->firstOrFail();
    }

    /**
     * @param \App\Http\Requests\Api\DTO\Branch $dto
     * @return Branch
     */
    public function create(\App\Http\Requests\Api\DTO\Branch $dto): Branch
    {
        $branch = new Branch;
        $branch->name = $dto->name;
        $branch->save();

        return $branch;
    }
}
