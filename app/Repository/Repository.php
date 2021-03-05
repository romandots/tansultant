<?php

namespace App\Repository;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    abstract protected function getQuery(): \Illuminate\Database\Eloquent\Builder;

    /**
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): Model
    {
        return $this->getQuery()
            ->where('id', $id)
            ->firstOrFail();
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }
}