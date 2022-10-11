<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Components\Loader;
use App\Models\Student;
use Illuminate\Console\Command;

class StudentCreditAdd extends Command
{
    protected $signature = 'student:credit:add';
    protected $description = 'Add credits to Student`s Customer';

    public function handle(): void
    {
        $customer = null;
        /** @var Student $student */
        while (null === $customer) {
            $studentId = $this->ask('Student ID:');
            $student = Loader::students()->getById($studentId);

            if (null === $student) {
                continue;
            }

            if (null === $student->customer) {
                $this->error("К выбранному студенту ({$student->name}) не привязан клиент");
                continue;
            }

            $customer = $student->customer;
        }

        $this->info("Найден студент: {$student->name}");
        $this->info("Клиент: {$student->customer}");

        $amount = 0;
        while(0 === $amount) {
            $amount = (int)$this->ask('Сколько кредитов добавить (списать):');
        }

        $comment = (string)$this->ask('Комментарий к ' . ($amount > 0 ? 'зачислению' : 'списанию') . ':');
        $user = Loader::users()->findByUsername('admin');

        $credit = Loader::credits()->createIncome($student->customer, $amount, $comment, $user);

        $this->info("Кредиты ({$credit->amount}₽) добавлены клиенту {$student->customer}");
    }
}
