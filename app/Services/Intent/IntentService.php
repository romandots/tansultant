<?php
/**
 * File: IntentService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Intent;

use App\Models\Intent;
use App\Models\Visit;
use App\Repository\IntentRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class IntentService
 * @package App\Services\Intent
 */
class IntentService
{
    /**
     * @var IntentRepository
     */
    private $repository;

    /**
     * @param IntentRepository $repository
     */
    public function __construct(IntentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Update status for every lesson visit intent:
     * - if student showed up - set VISITED
     * - otherwise set NOSHOW
     *
     * @param Collection|Visit[] $visits
     * @param Collection|Intent[] $intents
     */
    public function updateIntents(Collection $visits, Collection $intents): void
    {
        foreach ($intents as $intent) {
            if (0 === $visits->where('student_id', $intent->student_id)->count()) {
                $this->repository->setNoShow($intent);
            } else {
                $this->repository->setVisited($intent);
            }
        }
    }
}
