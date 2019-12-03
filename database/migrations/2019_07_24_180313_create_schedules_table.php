<?php
/**
 * File: 2019_07_24_180313_create_schedules_table.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

use App\Models\Course;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSchedulesTable
 */
class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('schedules', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->index();
            $table->uuid('classroom_id')->nullable()->index();
            $table->uuid('course_id')->index();
            $table->date('starts_at')->nullable()->index();
            $table->date('ends_at')->nullable()->index();
            $table->unsignedInteger('duration')->default(60)->comment('In minutes');
            $table->time('monday')->nullable()->index();
            $table->time('tuesday')->nullable()->index();
            $table->time('wednesday')->nullable()->index();
            $table->time('thursday')->nullable()->index();
            $table->time('friday')->nullable()->index();
            $table->time('saturday')->nullable()->index();
            $table->time('sunday')->nullable()->index();
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')
                ->on(Course::TABLE)
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
}
