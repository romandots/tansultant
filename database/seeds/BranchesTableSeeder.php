<?php
/**
 * File: BranchesTableSeeder.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */
declare(strict_types=1);

use App\Repository\BranchRepository;
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
     * BranchesTableSeeder constructor.
     * @param BranchRepository $branchRepository
     */
    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
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
            $dto = new \App\Http\Requests\Api\DTO\Branch;
            $dto->name = 'Студия';

            $this->branchRepository->create($dto);
        }
    }
}
