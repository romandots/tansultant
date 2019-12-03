<?php
/**
 * File: 2019_07_26_154006_create_intents_table.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

use App\Models\Lesson;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateIntentsTable
 */
class CreateIntentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('intents', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id')->index();
            $table->uuid('manager_id')->nullable()->index();
            $table->uuid('event_id');
            $table->text('event_type')->index();
            $table->text('status')->index();
            $table->timestamps();

            $table->index(['event_id', 'event_type'], 'morph_intents_event_id');

            $table->foreign('student_id')
                ->references('id')
                ->on(\App\Models\Student::TABLE)
                ->onDelete('restrict');

            $table->foreign('manager_id')
                ->references('id')
                ->on(\App\Models\User::TABLE);
        });

        \convertPostgresColumnTextToEnum('intents', 'event_type', [
            Lesson::class,
            '\App\Models\Event'
        ]);

        \convertPostgresColumnTextToEnum('intents', 'status', [
            'expecting',
            'visited',
            'no-show',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE intents_event_type CASCADE');
        \DB::unprepared('DROP TYPE intents_status CASCADE');
        Schema::dropIfExists('intents');
    }
}
