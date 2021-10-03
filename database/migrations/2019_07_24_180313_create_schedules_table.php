<?php
/**
 * File: 2019_07_24_180313_create_schedules_table.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateSchedulesTable
 */
class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create(
            'schedules',
            static function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->text('cycle')->index();
                $table->text('weekday')->index()->nullable();
                $table->date('from_date')->index()->nullable();
                $table->date('to_date')->index()->nullable();
                $table->time('starts_at')->index();
                $table->time('ends_at')->index();
                $table->uuid('branch_id')->nullable()->index();
                $table->uuid('classroom_id')->nullable()->index();
                $table->uuid('course_id')->nullable()->index();
                $table->timestamp('created_at');
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();

                $table->foreign('course_id')
                    ->references('id')
                    ->on(\App\Models\Course::TABLE)
                    ->onDelete('cascade');
            }
        );

        \convertPostgresColumnTextToEnum('schedules', 'cycle', [
            'once',
            'month',
            'week',
            'day',
        ]);
        \convertPostgresColumnTextToEnum('schedules', 'weekday', [1, 2, 3, 4, 5, 6, 7,]);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE schedules_cycle CASCADE');
        \DB::unprepared('DROP TYPE schedules_weekday CASCADE');
        Schema::dropIfExists('schedules');
    }
}
