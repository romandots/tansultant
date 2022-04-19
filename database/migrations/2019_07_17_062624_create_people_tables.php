<?php
/**
 * File: 2019_07_17_062624_create_people_tables.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreatePeopleTables
 */
class CreatePeopleTables extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create('people', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('last_name')->nullable();
            $table->text('first_name')->nullable();
            $table->text('patronymic_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('gender')->nullable();
            $table->text('phone')->unique()->nullable();
            $table->text('email')->unique()->nullable();
            $table->text('picture')->nullable();
            $table->text('picture_thumb')->nullable();
            $table->text('instagram_username')->nullable();
            $table->text('telegram_username')->nullable();
            $table->text('vk_uid')->nullable();
            $table->text('facebook_uid')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['last_name', 'first_name', 'patronymic_name', 'birth_date', 'deleted_at'], 'unique_bio');
        });

        \convertPostgresColumnTextToEnum('people', 'gender', \App\Models\Enum\Gender::cases());

        Schema::create('customers', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->uuid('person_id')->nullable()->index();
            $table->timestamp('created_at');
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('restrict');
        });

        Schema::create('students', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->text('card_number')->nullable();
            $table->text('status');
            $table->uuid('person_id')->nullable()->index();
            $table->uuid('customer_id')->nullable()->index();
            $table->timestamp('created_at');
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('restrict');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('restrict');
        });

        \convertPostgresColumnTextToEnum('students', 'status', \App\Models\Enum\StudentStatus::cases());

        Schema::create('instructors', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->text('description')->nullable();
            $table->text('picture')->nullable();
            $table->text('status');
            $table->boolean('display')->default(true);
            $table->uuid('person_id')->nullable()->index();
            $table->timestamp('created_at');
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('restrict');
        });

        \convertPostgresColumnTextToEnum('instructors', 'status', \App\Models\Enum\InstructorStatus::cases());

        Schema::table('users', static function (Blueprint $table) {
            $table->uuid('person_id')->nullable()->index();

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('restrict');
        });

        Schema::create('contracts', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('serial')->index();
            $table->unsignedInteger('number')->index();
            $table->uuid('branch_id')->nullable()->index();
            $table->uuid('customer_id')->nullable()->index();
            $table->uuid('student_id')->nullable()->index();
            $table->text('status');
            $table->timestamp('created_at');
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['serial', 'number'], 'unique_serial_number');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('restrict');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('restrict');
        });

        \convertPostgresColumnTextToEnum('contracts', 'status', \App\Models\Enum\ContractStatus::cases());

    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE people_gender CASCADE');
        \DB::unprepared('DROP TYPE students_status CASCADE');
        \DB::unprepared('DROP TYPE instructors_status CASCADE');
        \DB::unprepared('DROP TYPE contracts_status CASCADE');

        Schema::table('users', static function (Blueprint $table) {
            $table->dropForeign('users_person_id_foreign');
            $table->dropColumn('person_id');
        });

        Schema::dropIfExists('contracts');
        Schema::dropIfExists('students');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('people');
    }
}
