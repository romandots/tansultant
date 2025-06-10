<?php

declare(strict_types=1);

namespace App\Components\Formula;

use App\Common\BaseComponentService;
use App\Common\Contracts\DtoWithUser;
use App\Components\Formula\Entity\ConditionedEquation;
use App\Models\Enum\FormulaVar;
use App\Models\Formula;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Formula::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Dto $dto
     * @return Formula
     * @throws \Throwable
     */
    public function create(DtoWithUser $dto): Model
    {
        $this->checkFormulaEquation($dto->equation);
        return parent::create($dto);
    }

    /**
     * @param Formula $record
     * @param Dto $dto
     * @return void
     * @throws \Throwable
     */
    public function update(Model $record, DtoWithUser $dto): void
    {
        $this->checkFormulaEquation($dto->equation);
        parent::update($record, $dto);
    }


    public function checkFormulaEquation(string $equations): void
    {
        $conditionedEquations = $this->getConditionedEquations($equations);
        foreach ($conditionedEquations as $conditionedEquation) {
            $conditionedEquation->checkEquation();
        }
    }

    public function describeFormulaEquation(?string $equation): string
    {
        if (null === $equation) {
            return '';
        }

        $conditionedEquations = $this->getConditionedEquations($equation);

        if (count($conditionedEquations) === 1 && empty($conditionedEquations[0]->condition)) {
            return $conditionedEquations[0]->describeEquation();
        }

        $descriptions = [];
        foreach ($conditionedEquations as $conditionedEquation) {
            $descriptions[] = (
                $conditionedEquation->condition
                    ? 'Если ' . $conditionedEquation->describeCondition() . ', то '
                    : 'Иначе '
                ) . $conditionedEquation->describeEquation();
        }
        return implode("; \n", $descriptions);
    }

    public function calculateLessonPayoutAmount(Lesson $lesson, Formula $formula): float
    {
        $values = $this->getEquationValuesForLesson($lesson);
        $conditionedEquations = $this->getConditionedEquations($formula->equation, $values);
        $result = $this->evaluateAppropriateEquation($conditionedEquations);
        return round($result, 2);
    }

    /**
     * @param string $equationsWithConditionsString
     * @param array $values
     * @return ConditionedEquation[]
     */
    protected function getConditionedEquations(string $equationsWithConditionsString, array $values = []): array
    {
        $sets = explode('|', $equationsWithConditionsString);
        return array_map(
            static fn (string $conditionedEquation) => ConditionedEquation::create($conditionedEquation, $values), $sets
        );
    }

    /**
     * @param ConditionedEquation[] $formulaEquations
     * @return float
     */
    protected function evaluateAppropriateEquation(array $formulaEquations): float
    {
        foreach ($formulaEquations as $formulaEquation) {
            if ($formulaEquation->evaluateCondition()) {
                return $formulaEquation->evaluateEquation();
            }
        }

        return 0;
    }

    protected function getEquationValuesForLesson(Lesson $lesson): array
    {
        return [
            FormulaVar::ALL_VISITS->value => (int)$lesson->visits_count,
            FormulaVar::VISITS_BY_SUBSCRIPTION->value => (int)$lesson->getVisitsBySubscriptionCount(),
            FormulaVar::VISITS_BY_PAYMENT->value => (int)$lesson->getVisitsByPaymentCount(),
            FormulaVar::ACTIVE_STUDENTS->value => (int)$lesson->getActiveStudentsCount(),
            FormulaVar::INACTIVE_STUDENTS->value => (int)$lesson->getInactiveStudentsCount(),
            FormulaVar::ACTIVE_SUBSCRIPTIONS->value => (int)$lesson->course->active_subscriptions_count,
            FormulaVar::HOUR->value => (float)$lesson->getPeriodInHours(),
            FormulaVar::MINUTE->value => (int)$lesson->getPeriodInMinutes(),
        ];
    }
}