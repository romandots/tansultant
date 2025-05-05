<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('id_maps', static function (Blueprint $table) {
            $table->string('entity');
            $table->string('old_id');
            $table->uuid('new_id');
            $table->primary(['entity', 'old_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('id_maps');
    }
};
