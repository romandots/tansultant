<?php
/**
 * File: 2014_10_12_000000_create_users_table.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersTable
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->text('username')->index();
            $table->text('status')->index();
            $table->text('password');
            $table->text('remember_token')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['username', 'deleted_at'], 'unique_username');
        });

        \convertPostgresColumnTextToEnum('users', 'status', [
            'pending',
            'approved',
            'disabled',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE users_status CASCADE');
        Schema::dropIfExists('users');
    }
}
