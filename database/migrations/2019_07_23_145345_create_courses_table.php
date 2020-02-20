<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateCoursesTable
 */
class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create('courses', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->boolean('display');
            $table->json('age_restrictions');
            $table->text('picture')->nullable();
            $table->text('picture_thumb')->nullable();
            $table->text('status')->index();
            $table->uuid('instructor_id')->nullable()->index();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('instructor_id')
                ->references('id')
                ->on(\App\Models\Instructor::TABLE)
                ->onDelete('restrict');
        });

        \convertPostgresColumnTextToEnum('courses', 'status', [
            'pending',
            'active',
            'disabled',
        ]);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE courses_status CASCADE');
        Schema::dropIfExists('courses');
    }
}
