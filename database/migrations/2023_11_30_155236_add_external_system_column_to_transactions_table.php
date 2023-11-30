<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', static function (Blueprint $table) {
            $table->text('external_system')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', static function (Blueprint $table) {
            $table->dropColumn('external_system');
        });
    }
};
