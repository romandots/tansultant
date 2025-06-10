<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Adapters\Telegram\TelegramNotificationChannel;
use App\Common\Traits\WithLogger;
use App\Models\Customer;
use App\Models\Person;
use App\Models\Student;
use App\Services\CustomerService\CustomerServiceException;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class TelegramNotification extends Notification
{
    use Queueable, WithLogger;

    protected Person $person;

    abstract public function getMessage(): string;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function via($notifiable): array
    {
        return [TelegramNotificationChannel::class];
    }

    protected function getStudent(): Student
    {
        $student = $this->person->student;
        if (null === $student) {
            throw new CustomerServiceException('Student not found', [], 404);
        }

        return $student;
    }

    protected function getCustomer(): Customer
    {
        $customer = $this->getStudent()->customer;
        if (null === $customer) {
            throw new CustomerServiceException('Customer not found', [], 404);
        }

        return $customer;
    }

    protected function isPersonStudent(): bool
    {
        try {
            $this->getStudent();
            return true;
        } catch (CustomerServiceException) {
            return false;
        }
    }

    protected function isPersonCustomer(): bool
    {
        try {
            $this->getCustomer();
            return true;
        } catch (CustomerServiceException) {
            return false;
        }
    }
}
