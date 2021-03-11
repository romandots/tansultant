<?php
/**
 * File: BranchRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\ManagerApi\DTO\StoreBranch;
use App\Models\Branch;
use Carbon\Carbon;
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
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Branch
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findWithDeleted(string $id): ?Branch
    {
        return Branch::query()
            ->where('id', $id)
            ->firstOrFail();
    }

    /**
     * @param StoreBranch $dto
     * @return Branch
     * @throws \Exception
     */
    public function create(StoreBranch $dto): Branch
    {
        $branch = new Branch;
        $branch->id = \uuid();
        $branch->created_at = Carbon::now();

        $this->fill($branch, $dto);
        $branch->save();

        return $branch;
    }

    /**
     * @param Branch $branch
     * @param StoreBranch $dto
     * @throws \Exception
     */
    public function update(Branch $branch, StoreBranch $dto): void
    {
        $branch->updated_at = Carbon::now();

        $this->fill($branch, $dto);
        $branch->save();
    }

    /**
     * @param Branch $branch
     * @throws \Exception
     */
    public function delete(Branch $branch): void
    {
        $branch->deleted_at = Carbon::now();
        $branch->save();
    }

    /**
     * @param Branch $branch
     * @throws \Exception
     */
    public function restore(Branch $branch): void
    {
        $branch->deleted_at = null;
        $branch->save();
    }

    /**
     * @param Branch $branch
     * @param StoreBranch $dto
     */
    private function fill(Branch $branch, StoreBranch $dto): void
    {
        $branch->name = $dto->name;
        $branch->summary = $dto->summary;
        $branch->description = $dto->description;
        $branch->phone = $dto->phone;
        $branch->email = $dto->email;
        $branch->url = $dto->url;
        $branch->vk_url = $dto->vk_url;
        $branch->facebook_url = $dto->facebook_url;
        $branch->telegram_username = $dto->telegram_username;
        $branch->instagram_username = $dto->instagram_username;
        $branch->address = [
            'country' => $dto->address['country'] ?? null,
            'city' => $dto->address['city'] ?? null,
            'street' => $dto->address['street'] ?? null,
            'building' => $dto->address['building'] ?? null,
            'coordinates' => isset($dto->address['coordinates'][0], $dto->address['coordinates'][1])
                ? [$dto->address['coordinates'][0], $dto->address['coordinates'][1]] : null,
        ];
        $branch->number = $dto->number;
    }

    /**
     * @return int
     */
    public function getNextNumberValue(): int
    {
        return (int)(Branch::query()->max('number')) + 1;
    }
}
