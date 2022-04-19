<?php
/**
 * File: PriceService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Price;

use App\Common\BaseService;

/**
 * Class PriceService
 * @package App\Services\Price
 */
class PriceService extends BaseService
{
    /**
     * @todo Implement price policies
     *
     * @param \App\Models\Lesson $lesson
     * @param \App\Models\Student|null $student
     * @return int
     */
    public function calculateLessonVisitPrice(\App\Models\Lesson $lesson, ?\App\Models\Student $student): int
    {
        return 100;
    }
}
