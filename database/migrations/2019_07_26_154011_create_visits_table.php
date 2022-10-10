<?php
/**
 * File: 2019_07_26_154006_create_visits_table.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateVisitsTable
 */
class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create('visits', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id')->index();
            $table->uuid('manager_id')->nullable()->index();
            $table->text('event_type');
            $table->uuid('event_id');
            $table->text('payment_type');
            $table->timestamps();

            $table->index(['event_id', 'event_type'], 'morph_visits_event_id');

            $table->foreign('student_id')
                ->references('id')
                ->on(\App\Models\Student::TABLE)
                ->onDelete('restrict');

            $table->foreign('manager_id')
                ->references('id')
                ->on(\App\Models\User::TABLE);
        });

        \convertPostgresColumnTextToEnum('visits','event_type', \App\Models\Enum\VisitEventType::cases());
        \convertPostgresColumnTextToEnum('visits','payment_type', \App\Models\Enum\VisitPaymentType::cases());
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE visits_event_type CASCADE');
        \DB::unprepared('DROP TYPE visits_payment_type CASCADE');
        Schema::dropIfExists('visits');
    }
}
