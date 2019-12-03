<?php
/**
 * File: 2019_07_31_050943_create_classrooms_table.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateClassroomsTable
 */
class CreateClassroomsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create('classrooms', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->index();
            $table->uuid('branch_id')->index();
            $table->string('color')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('number')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')
                ->references('id')
                ->on(\App\Models\Branch::TABLE)
                ->onDelete('cascade');
        });

        Schema::table('lessons', static function (Blueprint $table) {
            $table->foreign('classroom_id')
                ->references('id')
                ->on(\App\Models\Classroom::TABLE);
        });

        Schema::table('schedules', static function (Blueprint $table) {
            $table->foreign('classroom_id')
                ->references('id')
                ->on(\App\Models\Classroom::TABLE);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        Schema::table('lessons', static function (Blueprint $table) {
            $table->dropForeign('lessons_classroom_id_foreign');
        });

        Schema::table('schedules', static function (Blueprint $table) {
            $table->dropForeign('schedules_classroom_id_foreign');
        });

        Schema::dropIfExists('classrooms');
    }
}
