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
        Schema::create('default_accounts', function (Blueprint $table) {
            $table->uuid('branch_id')->index();
            $table->uuid('account_id')->index();
            $table->text('transfer_type')->index();

            $table->primary(['branch_id', 'transfer_type']);
        });

        \convertPostgresColumnTextToEnum('default_accounts', 'transfer_type', \App\Models\Enum\TransactionTransferType::cases());

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        \DB::unprepared('DROP TYPE accounts_type CASCADE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \DB::unprepared('DROP TYPE default_accounts_transfer_type CASCADE');
        Schema::dropIfExists('default_accounts');

        Schema::table('accounts', function (Blueprint $table) {
            $table->text('type')->index()->nullable();
        });

        \convertPostgresColumnTextToEnum('accounts', 'type', \App\Models\Enum\AccountType::cases());
    }
};
