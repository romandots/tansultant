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
        Schema::create('credits', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->index();
            $table->uuid('transaction_id')->nullable()->index();
            $table->text('name');
            $table->integer('amount');
            $table->timestamp('created_at', 0)->nullable();
        });

        Schema::create('payments', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('amount');
            $table->text('name');
            $table->uuid('credit_id')->index();
            $table->uuid('bonus_id')->nullable()->index();
            $table->timestamp('created_at', 0)->nullable();

            $table->foreign('credit_id')
                ->references('id')
                ->on('credits')
                ->cascadeOnDelete();

            $table->foreign('bonus_id')
                ->references('id')
                ->on('bonuses')
                ->cascadeOnDelete();
        });

        Schema::table('credits', static function (Blueprint $table) {
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();

            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->cascadeOnDelete();
        });

        Schema::table('visits', static function (Blueprint $table) {
            $table->uuid('payment_id')->nullable()->index();
            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->cascadeOnDelete();
        });

        Schema::create('subscription_has_payments', static function (Blueprint $table) {
            $table->uuid('subscription_id')->index();
            $table->uuid('payment_id')->index();
            $table->timestamp('created_at');

            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
                ->onDelete('cascade');

            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('cascade');

            $table->primary(['subscription_id', 'payment_id'],
                'subscription_has_payments_primary');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_has_payments');

        Schema::table('visits', static function (Blueprint $table) {
            $table->dropForeign('visits_payment_id_foreign');
            $table->dropColumn('payment_id');
        });

        Schema::table('credits', static function (Blueprint $table) {
            $table->dropForeign('credits_transactions_id_foreign');
        });

        Schema::table('payments', static function (Blueprint $table) {
            $table->dropForeign('payments_credit_id_foreign');
            $table->dropForeign('payments_bonus_id_foreign');
        });

        Schema::dropIfExists('payments');
        Schema::dropIfExists('credits');
    }
};
