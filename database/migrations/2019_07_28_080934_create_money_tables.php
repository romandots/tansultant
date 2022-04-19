<?php
/**
 * File: 2019_07_28_080934_create_money_tables.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

use App\Models\Branch;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Visit;
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
            $table->text('owner_type')->index();
            $table->uuid('owner_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        \convertPostgresColumnTextToEnum('accounts', 'type', \App\Models\Enum\AccountType::cases());
        \convertPostgresColumnTextToEnum('accounts', 'owner_type', \App\Models\Enum\AccountOwnerType::cases());

        Schema::create('payments', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->integer('amount');
            $table->text('type')->index();
            $table->text('transfer_type')->index();
            $table->text('status')->index();
            $table->text('object_type')->nullable()->index();
            $table->uuid('object_id')->nullable()->index();
            $table->uuid('account_id')->index();
            $table->uuid('related_id')->nullable()->index();
            $table->text('external_id')->nullable()->index();
            $table->uuid('user_id')->index();
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

        \convertPostgresColumnTextToEnum('payments', 'type', \App\Models\Enum\PaymentType::cases());
        \convertPostgresColumnTextToEnum('payments', 'transfer_type', \App\Models\Enum\PaymentTransferType::cases());
        \convertPostgresColumnTextToEnum('payments', 'status', \App\Models\Enum\PaymentStatus::cases());
        \convertPostgresColumnTextToEnum('payments', 'object_type', \App\Models\Enum\PaymentObjectType::cases());

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
            $table->text('name');
            $table->integer('amount');
            $table->text('type')->index();
            $table->text('status')->index();
            $table->uuid('account_id')->index();
            $table->uuid('promocode_id')->nullable()->index();
            $table->uuid('user_id')->nullable()->index();
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
        \DB::unprepared('DROP TYPE accounts_owner_type CASCADE');

        \DB::unprepared('DROP TYPE payments_status CASCADE');
        \DB::unprepared('DROP TYPE payments_object_type CASCADE');
        \DB::unprepared('DROP TYPE payments_type CASCADE');
        \DB::unprepared('DROP TYPE payments_transfer_type CASCADE');

        \DB::unprepared('DROP TYPE bonuses_type CASCADE');
        \DB::unprepared('DROP TYPE bonuses_status CASCADE');

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
