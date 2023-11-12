<?php

namespace App\Services\Import;

use App\Components\Loader;
use App\Models\Enum\TariffStatus;
use App\Models\Tariff;
use App\Services\Import\Maps\TariffsMap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ImportTariffsService extends ImportService
{
    protected string $table = 'ticket_types';
    protected string $mapClass = TariffsMap::class;

    protected function getTag(\stdClass $record): string
    {
        return '#' . $record->id . ' (' . $record->ticket_type_name . ')';
    }

    protected function askDetails(): void
    {
        $this->buildMap();
    }

    protected function processImportRecord(\stdClass $record): Model
    {
        \DB::beginTransaction();

        try {
            $record = $this->findOrCreateTariff($record);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }

        \DB::commit();

        return $record;
    }

    protected function prepareImportQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->dbConnection
            ->table($this->table)
            ->orderBy('id', 'asc');
    }

    private function findOrCreateTariff(\stdClass $record): Tariff
    {
        try {
            $tariff = Loader::tariffs()->findBy('name', $record->ticket_type_name);
        } catch (ModelNotFoundException $exception) {
            $tariff = $this->createTariff($record);
        }

        return $tariff;
    }

    private function createTariff(\stdClass $record): Tariff
    {
        $prolongationPrice = $record->default_price ? $record->default_price * 0.9 : null;
        $tariff = Loader::tariffs()->make([
            'id' => \uuid(),
            'name' => $record->ticket_type_name,
            'price' => $record->default_price,
            'prolongation_price' => $prolongationPrice,
            'courses_limit' => null,
            'visits_limit' => $record->default_periods ? $record->default_periods / 2 : 1,
            'days_limit' => $record->default_period,
            'holds_limit' => $record->default_frosts,
            'status' => TariffStatus::ACTIVE,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            //ticket_type_group
            //ticket_type_name
            //default_period
            //default_periods
            //default_skips
            //default_frosts
            //default_shifts
            //default_guests
            //default_promo
            //default_multiclass
            //default_super
            //lesson_types
            //default_classes_included
            //default_classes_excluded
            //groups_included
            //groups_excluded
            //default_price
            //ticket_type_color
            //ticket_type_active
            //ticket_type_user_id
        ]);

        Loader::tariffs()->getRepository()->save($tariff);

        return $tariff;
    }
}