<?php

namespace Tests\Unit\Service;

use App\Components\Loader;
use App\Models\Formula;
use App\Models\Lesson;
use App\Models\Visit;
use Tests\TestCase;

class FormulaServiceTest extends TestCase
{
    private \App\Components\Formula\Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Loader::formulas()->getService();
    }

    public function testCalculateLessonPayoutAmount(): void
    {
        $lesson = Lesson::factory()->create();
        assert($lesson instanceof Lesson);
        $formula = (new Formula([
            'equation' => null
        ]))->save();
        assert($formula instanceof Formula);
        $amount = $this->service->calculateLessonPayoutAmount($lesson, $formula);
        $this->assertEquals(0, $amount);

        Visit::factory(3)->create([
            'lesson_id' => $lesson->id,
        ]);

        $formula->equation = '100 * П';
        $formula->save();

        $amount = $this->service->calculateLessonPayoutAmount($lesson, $formula);
        $this->assertEquals(300, $amount);
    }
}
