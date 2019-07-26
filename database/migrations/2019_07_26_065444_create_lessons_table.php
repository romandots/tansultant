<?php
/**
 * File: 2019_07_26_065444_create_lessons_table.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

use App\Models\Lesson;
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
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedInteger('branch_id')->nullable()->index();
            $table->unsignedInteger('course_id')->nullable()->index();
            $table->unsignedInteger('schedule_id')->nullable()->index();
            $table->unsignedInteger('classroom_id')->nullable()->index();
            $table->unsignedInteger('instructor_id')->nullable()->index();
            $table->unsignedInteger('controller_id')->nullable()->index();
            $table->enum('type', Lesson::TYPES)->default(Lesson::TYPE_LESSON);
            $table->enum('status', Lesson::STATUSES)->default(Lesson::STATUS_BOOKED);
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
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
}
