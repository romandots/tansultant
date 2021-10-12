<?php

namespace App\Services\Classroom;

use App\Http\Requests\DTO\FilteredDto;
use App\Http\Requests\ManagerApi\DTO\SearchLessonsFilterDto;
use App\Models\Classroom;
use App\Repository\BaseRepository;
use App\Repository\ClassroomRepository;
use App\Services\BaseService;
use JetBrains\PhpStorm\Pure;

class ClassroomService extends BaseService
{
    protected ClassroomRepository $repository;

    public function __construct(ClassroomRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }

    public function getModelClassName(): string
    {
        return Classroom::class;
    }

    #[Pure] public function makeSearchFilterDto(): FilteredDto
    {
        return new SearchLessonsFilterDto();
    }

    public function create(\App\Http\Requests\ManagerApi\DTO\Classroom $storeClassroom): Classroom
    {
        return $this->repository->create($storeClassroom);
    }

    public function update(Classroom $record, \App\Http\Requests\ManagerApi\DTO\Classroom $classroomDto): void
    {
        $this->repository->update($record, $classroomDto);
        //event(ClassroomUpdatedEvent)
    }

    public function delete(Classroom $record): void
    {
        $this->repository->delete($record);
        //event(ClassroomDeletedEvent)
    }
}