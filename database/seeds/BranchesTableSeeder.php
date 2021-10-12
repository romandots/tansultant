<?php
/**
 * File: BranchesTableSeeder.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */
declare(strict_types=1);

use App\Repository\BranchRepository;
use App\Repository\ClassroomRepository;
use Illuminate\Database\Seeder;

/**
 * Class BranchesTableSeeder
 */
class BranchesTableSeeder extends Seeder
{
    /**
     * @var BranchRepository
     */
    private $branchRepository;

    /**
     * @var ClassroomRepository
     */
    private $classroomRepository;

    /**
     * BranchesTableSeeder constructor.
     * @param BranchRepository $branchRepository
     * @param ClassroomRepository $classroomRepository
     */
    public function __construct(
        BranchRepository $branchRepository,
        ClassroomRepository $classroomRepository
    ) {
        $this->branchRepository = $branchRepository;
        $this->classroomRepository = $classroomRepository;
    }

    /**
     * Seed the application's database.
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        try {
            $branch = \App\Models\Branch::query()->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $branchDto = new \App\Http\Requests\ManagerApi\DTO\StoreBranch;
            $branchDto->name = 'Студия';

            $branch = $this->branchRepository->create($branchDto);
        }

        try {
            \App\Models\Classroom::query()->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $classroom = new \App\Http\Requests\ManagerApi\DTO\Classroom;
            $classroom->name = 'Зал А';
            $classroom->branch_id = $branch->id;

            $this->classroomRepository->create($classroom);
        }
    }
}
