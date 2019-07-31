<?php
/**
 * File: ClassroomRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\Api\DTO\Classroom as ClassroomDto;
use App\Models\Classroom;

/**
 * Class ClassroomRepository
 */
class ClassroomRepository
{
    /**
     * @param int $id
     * @return Classroom
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
        $classroom->name = $dto->name;
        $classroom->branch_id = $dto->branch_id;
        $classroom->color = $dto->color;
        $classroom->capacity = $dto->capacity;
        $classroom->number = $dto->number;
        $classroom->save();

        return $classroom;
    }
}
