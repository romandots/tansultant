<?php
/**
 * File: 2019_07_28_080934_create_money_tables.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateMoneyTables
 */
class CreateMoneyTables extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create('accounts', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('amount');
            $table->enum('type', \App\Models\Account::TYPES)->index();
            $table->enum('owner_type', \App\Models\Account::OWNER_TYPES)->index();
            $table->unsignedInteger('owner_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payments', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('amount');
            $table->enum('type', \App\Models\Payment::TYPES)
                ->default(\App\Models\Payment::TYPE_MANUAL)
                ->index();
            $table->enum('transfer_type', \App\Models\Payment::TRANSFER_TYPES)
                ->default(\App\Models\Payment::TRANSFER_TYPE_CASH)
                ->index();
            $table->enum('status', \App\Models\Payment::STATUSES)
                ->default(\App\Models\Payment::STATUS_PENDING)
                ->index();
            $table->enum('object_type', \App\Models\Payment::OBJECT_TYPES)->nullable()->index();
            $table->unsignedInteger('object_id')->nullable()->index();
            $table->uuid('account_id')->index();
            $table->uuid('related_id')->nullable()->index();
            $table->string('external_id')->nullable()->index();
            $table->unsignedInteger('user_id')->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['object_type', 'object_id'], 'morph_payments_object');

            $table->foreign('account_id')
                ->references('id')
                ->on(\App\Models\Account::TABLE)
                ->onDelete('restrict');

            $table->foreign('user_id')
                ->references('id')
                ->on(\App\Models\User::TABLE);
        });

        Schema::table('payments', static function (Blueprint $table) {
            $table->foreign('related_id')
                ->references('id')
                ->on(\App\Models\Payment::TABLE)
                ->onDelete('restrict');
        });

        Schema::table('visits', static function (Blueprint $table) {
            $table->foreign('payment_id')
                ->references('id')
                ->on(\App\Models\Payment::TABLE)
                ->onDelete('restrict');
        });

        Schema::table('lessons', static function (Blueprint $table) {
            $table->foreign('payment_id')
                ->references('id')
                ->on(\App\Models\Payment::TABLE)
                ->onDelete('restrict');
        });

        Schema::create('bonuses', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('amount');
            $table->enum('type', \App\Models\Bonus::TYPES)->index();
            $table->enum('status', \App\Models\Bonus::STATUSES)
                ->default(\App\Models\Bonus::STATUS_PENDING)
                ->index();
            $table->uuid('account_id')->index();
            $table->uuid('promocode_id]')->nullable()->index();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')
                ->references('id')
                ->on(\App\Models\Account::TABLE)
                ->onDelete('restrict');

//            $table->foreign('promocode_id')
//                ->references('id')
//                ->on(\App\Models\Promocode::TABLE)
//                ->onDelete('restrict');

            $table->foreign('user_id')
                ->references('id')
                ->on(\App\Models\User::TABLE);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        Schema::table('visits', static function (Blueprint $table) {
            $table->dropForeign('visits_payment_id_foreign');
        });

        Schema::table('lessons', static function (Blueprint $table) {
            $table->dropForeign('lessons_payment_id_foreign');
        });

        Schema::dropIfExists('bonuses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('accounts');
    }
}
