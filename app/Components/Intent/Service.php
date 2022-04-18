<?php

declare(strict_types=1);

namespace App\Components\Intent;

use App\Common\BaseService;
use App\Models\Intent;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            Intent::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * Update status for every lesson visit intent:
     * - if student showed up - set VISITED
     * - otherwise set NOSHOW
     *
     * @param Collection<Visit> $visits
     * @param Collection<Intent> $intents
     */
    public function updateIntents(Collection $visits, Collection $intents): void
    {
        foreach ($intents as $intent) {
            if (0 === $visits->where('student_id', $intent->student_id)->count()) {
                $this->getRepository()->setNoShow($intent);
            } else {
                $this->getRepository()->setVisited($intent);
            }
        }
    }
}