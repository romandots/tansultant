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
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->uuid('user_id')->index();
            $table->uuid('branch_id')->nullable()->index();
            $table->double('total_income')->nullable();
            $table->text('status')->index();
            $table->timestamp('created_at', 0)->nullable();
            $table->timestamp('closed_at', 0)->nullable();
            $table->timestamp('updated_at', 0)->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->cascadeOnDelete();
        });

        convertPostgresColumnTextToEnum('shifts', 'status', \App\Models\Enum\ShiftStatus::cases());

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('active_shift_id')->nullable()->index();

            $table->foreign('active_shift_id')
                ->references('id')
                ->on('shifts')
                ->cascadeOnDelete();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->uuid('shift_id')->nullable()->index();

            $table->foreign('shift_id')
                ->references('id')
                ->on('shifts')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_shift_id_foreign');
            $table->dropColumn(['shift_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_active_shift_id_foreign');
            $table->dropColumn(['active_shift_id']);
        });

        \DB::unprepared('DROP TYPE shifts_status CASCADE');

        Schema::dropIfExists('shifts');
    }
};
