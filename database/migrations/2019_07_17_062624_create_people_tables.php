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
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('patronymic_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('gender')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('picture')->nullable();
            $table->string('picture_thumb')->nullable();
            $table->string('instagram_username')->nullable();
            $table->string('telegram_username')->nullable();
            $table->string('vk_uid')->nullable();
            $table->string('vk_url')->nullable();
            $table->string('facebook_uid')->nullable();
            $table->string('facebook_url')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['last_name', 'first_name', 'patronymic_name', 'birth_date'], 'unique_person');
        });

        \convertPostgresColumnTextToEnum('people', 'gender', ['male', 'female']);

        Schema::create('customers', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('person_id')->nullable()->index();
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('restrict');
        });

        Schema::create('students', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->unsignedInteger('card_number')->nullable();
            $table->text('status');
            $table->uuid('person_id')->nullable()->index();
            $table->uuid('customer_id')->nullable()->index();
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('restrict');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('restrict');
        });

        \convertPostgresColumnTextToEnum('students', 'status', [
            'potential',
            'active',
            'recent',
            'former',
        ]);

        Schema::create('instructors', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('picture')->nullable();
            $table->text('status');
            $table->boolean('display')->default(true);
            $table->uuid('person_id')->nullable()->index();
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('restrict');
        });

        \convertPostgresColumnTextToEnum('instructors', 'status', [
            'hired',
            'freelance',
            'fired',
        ]);

        Schema::table('users', static function (Blueprint $table) {
            $table->uuid('person_id')->nullable()->index();

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('restrict');
        });

        Schema::create('contracts', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('serial')->index();
            $table->unsignedInteger('number')->index();
            $table->uuid('branch_id')->nullable()->index();
            $table->uuid('customer_id')->nullable()->index();
            $table->text('status');
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->timestamps();

            $table->unique(['serial', 'number'], 'unique_serial_number');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('restrict');
        });

        \convertPostgresColumnTextToEnum('contracts', 'status', [
            'pending',
            'signed',
            'terminated',
        ]);

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
