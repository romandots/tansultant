<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('log_records', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('action')->index();
            $table->text('object_type');
            $table->uuid('object_id')->index();
            $table->uuid('user_id')->index();
            $table->text('message');
            $table->jsonb('old_value')->nullable();
            $table->jsonb('new_value')->nullable();
            $table->timestamp('created_at');

            $table->foreign('user_id')
                ->references('id')
                ->on(\App\Models\User::TABLE)
                ->onDelete('restrict');
        });

        \convertPostgresColumnTextToEnum('log_records', 'action', \App\Models\Enum\LogRecordAction::cases());
        \convertPostgresColumnTextToEnum('log_records', 'object_type', \App\Models\Enum\LogRecordObjectType::cases());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE log_records_action CASCADE');
        \DB::unprepared('DROP TYPE log_records_object_type CASCADE');

        Schema::dropIfExists('log_records');
    }
}
