<?php
/**
 * File: 2019_07_26_065444_create_lessons_table.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateLessonsTable
 */
class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create('lessons', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('branch_id')->nullable()->index();
            $table->uuid('course_id')->nullable()->index();
            $table->uuid('schedule_id')->nullable()->index();
            $table->uuid('classroom_id')->nullable()->index();
            $table->uuid('instructor_id')->nullable()->index();
            $table->uuid('controller_id')->nullable()->index();
            $table->uuid('payment_id')->nullable()->index();
            $table->text('type');
            $table->text('status');
            $table->timestamp('starts_at')->index();
            $table->timestamp('ends_at')->index();
            $table->timestamp('closed_at')->nullable()->index();
            $table->timestamp('canceled_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('course_id')
                ->references('id')
                ->on(\App\Models\Course::TABLE)
                ->onDelete('cascade');

            $table->foreign('schedule_id')
                ->references('id')
                ->on(\App\Models\Schedule::TABLE);

            $table->foreign('instructor_id')
                ->references('id')
                ->on(\App\Models\Instructor::TABLE);

            $table->foreign('controller_id')
                ->references('id')
                ->on(\App\Models\User::TABLE);
        });

        \convertPostgresColumnTextToEnum('lessons', 'type', [
            'lesson',
            'event',
            'rent',
        ]);

        \convertPostgresColumnTextToEnum('lessons', 'status', [
            'booked',
            'ongoing',
            'passed',
            'canceled',
            'closed',
        ]);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE lessons_type CASCADE');
        \DB::unprepared('DROP TYPE lessons_status CASCADE');
        Schema::dropIfExists('lessons');
    }
}
