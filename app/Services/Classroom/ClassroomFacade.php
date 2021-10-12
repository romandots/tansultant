<?php

namespace App\Services\Classroom;

use App\Models\Classroom;
use App\Repository\ClassroomRepository;
use App\Repository\BaseRepository;
use App\Services\BaseFacade;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class ClassroomFacade extends BaseFacade
{
    protected ClassroomRepository $repository;
    protected ClassroomService $service;

    public function __construct(ClassroomRepository $repository, ClassroomService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }

    public function getService(): BaseService
    {
        return $this->service;
    }

    public function create(\App\Http\Requests\ManagerApi\DTO\Classroom $storeClassroom): Classroom
    {
        return $this->service->create($storeClassroom);
    }

    public function find(string $classroomId): Classroom
    {
        return $this->repository->find($classroomId);
    }

    public function findAndUpdate(string $id, \App\Http\Requests\ManagerApi\DTO\Classroom $classroomDto): Classroom
    {
        $record = $this->repository->find($id);
        $this->service->update($record, $classroomDto);

        return $record;
    }

    public function findAndDelete(string $classroomId): void
    {
        $record = $this->repository->find($classroomId);
        $this->service->delete($record);
    }

    public function restore(string $classroomId): void
    {
        $record = $this->repository->findWithDeleted($classroomId);
        $this->repository->restore($record);
    }
}