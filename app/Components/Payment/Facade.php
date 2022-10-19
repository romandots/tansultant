<?php

declare(strict_types=1);

namespace App\Components\Payment;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Bonus;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Visit;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Payment> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Payment> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Payment create(Dto $dto, array $relations = [])
 * @method \App\Models\Payment find(ShowDto $showDto)
 * @method \App\Models\Payment findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Payment findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createVisitPayment(Visit $visit, Student $student, ?Bonus $bonus, User $user): Payment
    {
        $price = (int)$visit->price;
        $name = trans('credit.withdrawals.visit', ['visit' => $visit->name]);

        return $this->getService()->createPayment($price, $name, $student, $bonus, $user);
    }

    public function createSubscriptionPayment(
        Subscription $subscription,
        Student $student,
        ?Bonus $bonus,
        User $user
    ): Payment {
        $price = (int)$subscription->tariff->price;
        $name = trans('credit.withdrawals.subscription', [
            'subscription' => $subscription->name,
            'student' => $subscription->student->name,
        ]);

        return $this->getService()->createPayment($price, $name, $student, $bonus, $user);
    }

    public function createSubscriptionProlongationPayment(
        Subscription $subscription,
        ?Bonus $bonus,
        User $user
    ): Payment {
        $price = (int)($subscription->tariff->prolongation_price ?? $subscription->tariff->price);
        $name = trans('credit.withdrawals.subscription_prolongation', [
            'subscription' => $subscription->name,
            'student' => $subscription->student->name,
        ]);

        return $this->getService()->createPayment($price, $name, $subscription->student, $bonus, $user);
    }
}