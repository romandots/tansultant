<?php

namespace App\Notifications\CustomerService;

use App\Models\Student;
use App\Notifications\TelegramNotification;
use Illuminate\Support\Collection;

use function morphos\Russian\pluralize;

class SubscriptionsNotification extends TelegramNotification
{
    public function getMessage(): string
    {
        if ($this->isPersonStudent()) {
            return $this->formatOneStudentSubscriptionsMessage($this->getStudent());
        }

        return $this->formatManyStudentsSubscriptionsMessage($this->getCustomer()->students);
    }

    protected function formatOneStudentSubscriptionsMessage(Student $student): string
    {
        return trans('customer_service.one_student_subscriptions',
            [
                'student' => $student->name,
                'subscriptions' => implode(
                    PHP_EOL,
                    $this->formatSubscriptionsList(
                        $student,
                        'customer_service.one_student_subscription_details'
                    )
                ),
            ]
        );
    }

    protected function formatManyStudentsSubscriptionsMessage(Collection $students): string
    {
        $subscriptionsByStudents = [];
        foreach ($students as $student) {
            $subscriptionsByStudents[] = "{$student->name}:" . PHP_EOL . implode(
                    PHP_EOL,
                    $this->formatSubscriptionsList(
                        $student,
                        'customer_service.many_students_subscription_details'
                    )
                );
        }

        return trans('customer_service.many_student_subscriptions',
            [
                'student' => $this->getStudent()->name,
                'subscriptions' => implode(
                    PHP_EOL,
                    $subscriptionsByStudents
                ),
            ]
        );
    }

    protected function formatSubscriptionsList(Student $student, string $transKey): array
    {
        $subscriptionsItems = [];
        foreach ($student->active_subscriptions as $subscription) {
            $name = "* {$subscription->name}";
            if ($subscription->courses_count > 0) {
                $name .= ': ' . implode(', ', array_values($subscription->courses_list));
            }
            $expiredAt = $subscription->expired_at->toFormattedDayDateString();
            $visitsLeft = pluralize($subscription->visits_left, trans('customer_service.nouns.visit'));

            $subscriptionsItems[] = trans($transKey, [
                'student' => $subscription->student->name,
                'name' => $name,
                'expired_at' => $expiredAt,
                'visits_left' => $visitsLeft,
            ]);
        }

        return $subscriptionsItems;
    }
}