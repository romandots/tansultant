<?php

namespace App\Services\Import;

use App\Components\Loader;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\Tariff;
use App\Services\Import\Maps\CoursesMap;
use App\Services\Import\Maps\StudentsMap;
use App\Services\Import\Maps\SubscriptionsMap;
use App\Services\Import\Maps\TariffsMap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intervention\Image\Exception\NotFoundException;

class ImportSubscriptionsService extends ImportService
{
    protected string $table = 'tickets';
    protected string $mapClass = SubscriptionsMap::class;
    protected int $batchSize = 5;

    private function getStudentsImportService(): ImportStudentsService
    {
        if (!isset($this->studentsImportService)) {
            $this->studentsImportService = new ImportStudentsService($this->cli, $this->dbConnection);
        }
        return $this->studentsImportService;
    }

    private function getCoursesMap(): CoursesMap
    {
        return $this->getMapper(CoursesMap::class);
    }

    private function getStudentsMap(): StudentsMap
    {
        return $this->getMapper(StudentsMap::class);
    }

    private function getTariffsMap(): TariffsMap
    {
        return $this->getMapper(TariffsMap::class);
    }

    protected function getTag(\stdClass $record): string
    {
        return '#' . $record->id . ' ' . $record->ticket_name . ' (' . $record->ticket_type . ')';
    }

    protected function askDetails(): void
    {
        //$this->buildMap();
    }

    protected function processImportRecord(\stdClass $record): Model
    {
        \DB::beginTransaction();

        try {
            $subscription = $this->findSubscription($record);
            if (null === $subscription) {
                $subscription = $this->createSubscription($record);
            }
            $this->subscribeToCourses($record, $subscription);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }

        \DB::commit();

        return $subscription;
    }

    protected function prepareImportQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->dbConnection
            ->table($this->table)
            ->whereRaw('expired > NOW()')
            ->orderBy('id', 'asc');
    }

    private function findSubscription(\stdClass $record): ?Subscription
    {
        try {
            $studentId = $this->getStudentsMap()->mapped($record->client_id);
            if (null === $studentId) {
                throw new \LogicException('Student (client) is not mapped');
            }
            return Loader::subscriptions()->getRepository()
                ->getQuery()
                ->where('name', $record->ticket_name)
                ->where('student_id', $studentId)
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return null;
        }
    }

    private function createSubscription(\stdClass $record): Subscription
    {
        $student = $this->getStudentsMap()->mappedRecord($record->client_id);
        $student ??= $this->createStudent($record->client_id);
        if (!($student instanceof Student)) {
            throw new Exceptions\ImportServiceException('Failed to create student');
        }

        $tariff = $this->getTariffsMap()->mappedRecord($record->ticket_type);
        $tariff ??= Loader::tariffs()->findBy('name', $record->ticket_type);
        if (!($tariff instanceof Tariff)) {
            throw new NotFoundException('Tariff not found');
        }

        $visitsLimit = (int)round(((int)$record->periods - (int)$record->periods_used) / 2);
        $visitsLimit = max($visitsLimit, 0);
        $holdsLimit = (int)($record->frosts - $record->frosts_used);
        $holdsLimit = max($holdsLimit, 0);
        $subscription = Loader::subscriptions()->make([
            'id' => \uuid(),
            'name' => $record->ticket_name ?? $tariff->name,
            'courses_limit' => null,
            'visits_limit' => $visitsLimit,
            'days_limit' => $record->period,
            'holds_limit' => $holdsLimit,
            'status' => SubscriptionStatus::ACTIVE,
            'student_id' => $student->id,
            'tariff_id' => $tariff->id,
            'created_at' => $record->created,
            'updated_at' => \Carbon\Carbon::now(),
            'activated_at' => \Carbon\Carbon::now(),
            'expired_at' => $record->expired,
            //studio_id
            //client_id
            //class_id
            //created
            //period
            //frozen
            //unfrozen
            //expired
            //periods
            //periods_used
            //skips
            //frosts
            //frosts_used
            //shifts
            //shifts_used
            //guests
            //guests_used
            //active
            //null
            //creation_timestamp
            //promo
            //ticket_type
            //ticket_name
            //multiclass
            //super
            //classes_included
            //classes_excluded
            //groups_included
            //groups_excluded
            //balance_record
            //balance_code
            //bonus_used
            //discount_id
            //original_price
            //price
            //comment
            //history
            //user_id
            //cache
            //status
        ]);

        Loader::subscriptions()->findOrSave($subscription, [
            'name',
            'student_id',
            'tariff_id',
            'created_at',
            'expired_at',
        ]);

        return $subscription;
    }

    private function subscribeToCourses(\stdClass $record, Subscription $subscription): void
    {
        try {
            $coursesIds = $this->getVisitedCoursesIds($record->id);
            $this->attachCoursesToSubscriptionAndTariff($subscription, $coursesIds);
        } catch (\Exception $e) {
            throw new \Exception('Failed to attach courses to subscription', 0, $e);
        }
    }

    private function getVisitedCoursesIds(int|string $ticketId): array
    {
        $classesIds = $this->dbConnection
            ->table('visits')
            ->join('lessons', 'lessons.id', '=', 'visits.lesson_id')
            ->where('visits.ticket_id', $ticketId)
            ->groupBy('lessons.class_id')
            ->get(['lessons.class_id'])
            ->pluck('class_id')
            ->toArray();

        $coursesIds = array_filter(
            array_map(function ($classId) {
                return $this->getCoursesMap()->mapped($classId);
            }, $classesIds)
        );

        return $coursesIds;
    }

    private function attachCoursesToSubscriptionAndTariff(Subscription $subscription, array $courses): void
    {
        if (empty($courses)) {
            return;
        }

        Loader::tariffs()->getRepository()->attachCourses($subscription->tariff, $courses);
        Loader::subscriptions()->getRepository()->attachCourses($subscription, $courses);
    }

    private function createStudent(int $clientId): ?Student
    {
        return $this->getStudentsImportService()->importRecordById($clientId);
    }

}