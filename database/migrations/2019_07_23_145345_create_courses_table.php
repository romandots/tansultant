<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCoursesTable
 */
class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(\App\Models\Course::TABLE, static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('age_restrictions')->nullable();
            $table->string('picture')->nullable();
            $table->string('picture_thumb')->nullable();
            $table->enum('status', \App\Models\Course::STATUSES)
                ->default(\App\Models\Course::STATUS_PENDING);
            $table->unsignedInteger('instructor_id')->nullable()->index();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('instructor_id')
                ->references('id')
                ->on(\App\Models\Instructor::TABLE)
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
}
