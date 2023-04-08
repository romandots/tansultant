<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('amount')->nullable();
            $table->uuid('branch_id')->index();
            $table->uuid('instructor_id')->index();
            $table->date('period_from');
            $table->date('period_to');
            $table->text('status');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->foreign('instructor_id')
                ->references('id')
                ->on(\App\Models\Instructor::TABLE)
                ->onDelete('restrict');

            $table->foreign('branch_id')
                ->references('id')
                ->on(\App\Models\Branch::TABLE)
                ->onDelete('restrict');
        });

        \convertPostgresColumnTextToEnum('payouts', 'status', \App\Models\Enum\PayoutStatus::cases());

        Schema::create('payout_has_lessons', function (Blueprint $table) {
            $table->integer('amount');
            $table->text('equation');
            $table->uuid('lesson_id')->index();
            $table->uuid('payout_id')->index();
            $table->uuid('formula_id')->index();
            $table->timestamp('created_at')->nullable();

            $table->primary(['lesson_id', 'payout_id'],
                'payout_has_lessons_primary');

            $table->foreign('lesson_id')
                ->references('id')
                ->on(\App\Models\Lesson::TABLE)
                ->onDelete('cascade');

            $table->foreign('payout_id')
                ->references('id')
                ->on(\App\Models\Payout::TABLE)
                ->onDelete('cascade');

            $table->foreign('formula_id')
                ->references('id')
                ->on(\App\Models\Formula::TABLE)
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
        Schema::dropIfExists('payout_has_lessons');
        \DB::unprepared('DROP TYPE payouts_status CASCADE');
        Schema::dropIfExists('payouts');
    }
};
