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
     */
    public function run(): void
    {
        try {
            $this->branchRepository->find(1);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $branch = new \App\Http\Requests\Api\DTO\Branch;
            $branch->name = 'Студия';

            $this->branchRepository->create($branch);
        }

        try {
            $this->classroomRepository->find(1);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $classroom = new \App\Http\Requests\PublicApi\DTO\Classroom;
            $classroom->name = 'Зал А';

            $this->classroomRepository->create($classroom);
        }
    }
}
