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
            $table->text('name');
            $table->text('type')->index();
            $table->uuid('branch_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        \convertPostgresColumnTextToEnum('accounts', 'type', \App\Models\Enum\AccountType::cases());

        Schema::create('transactions', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->integer('amount');
            $table->text('type')->index();
            $table->text('transfer_type')->index();
            $table->text('status')->index();
            $table->uuid('account_id')->index();
            $table->uuid('customer_id')->nullable()->index();
            $table->uuid('related_id')->nullable()->index();
            $table->text('external_id')->nullable()->index();
            $table->uuid('user_id')->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')
                ->references('id')
                ->on(\App\Models\Account::TABLE)
                ->onDelete('restrict');

            $table->foreign('customer_id')
                ->references('id')
                ->on(\App\Models\Customer::TABLE)
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on(\App\Models\User::TABLE);
        });

        \convertPostgresColumnTextToEnum('transactions', 'type', \App\Models\Enum\TransactionType::cases());
        \convertPostgresColumnTextToEnum('transactions', 'transfer_type', \App\Models\Enum\TransactionTransferType::cases());
        \convertPostgresColumnTextToEnum('transactions', 'status', \App\Models\Enum\TransactionStatus::cases());

        Schema::table('transactions', static function (Blueprint $table) {
            $table->foreign('related_id')
                ->references('id')
                ->on(\App\Models\Transaction::TABLE)
                ->onDelete('restrict');
        });

        Schema::create('bonuses', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->integer('amount');
            $table->text('type')->index();
            $table->text('status')->index();
            $table->uuid('customer_id')->index();
            $table->uuid('promocode_id')->nullable()->index();
            $table->uuid('user_id')->nullable()->index();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')
                ->references('id')
                ->on(\App\Models\Customer::TABLE)
                ->onDelete('restrict');

//            $table->foreign('promocode_id')
//                ->references('id')
//                ->on(\App\Models\Promocode::TABLE)
//                ->onDelete('restrict');

            $table->foreign('user_id')
                ->references('id')
                ->on(\App\Models\User::TABLE);
        });

        \convertPostgresColumnTextToEnum('bonuses', 'type', \App\Models\Enum\BonusType::cases());
        \convertPostgresColumnTextToEnum('bonuses', 'status', \App\Models\Enum\BonusStatus::cases());
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE accounts_type CASCADE');

        \DB::unprepared('DROP TYPE transactions_status CASCADE');
        \DB::unprepared('DROP TYPE transactions_type CASCADE');
        \DB::unprepared('DROP TYPE transactions_transfer_type CASCADE');

        \DB::unprepared('DROP TYPE bonuses_type CASCADE');
        \DB::unprepared('DROP TYPE bonuses_status CASCADE');

        Schema::dropIfExists('bonuses');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('accounts');
    }
}
