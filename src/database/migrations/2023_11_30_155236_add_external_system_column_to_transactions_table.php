<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', static function (Blueprint $table) {
            $table->text('external_id')->nullable()->index();
            $table->text('external_system')->nullable()->index();
        });
        Schema::table('transactions', static function (Blueprint $table) {
            $table->text('external_system')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('accounts', static function (Blueprint $table) {
            $table->dropColumn(['external_system', 'external_id']);
        });
        Schema::table('transactions', static function (Blueprint $table) {
            $table->dropColumn('external_system');
        });
    }
};
