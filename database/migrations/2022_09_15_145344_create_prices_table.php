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
        Schema::create(\App\Models\Price::TABLE, static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->float('price');
            $table->timestamp('created_at', 0);
            $table->timestamp('updated_at', 0)->nullable();
            $table->timestamp('deleted_at', 0)->nullable();
        });

        Schema::table(\App\Models\Student::TABLE, static function (Blueprint $table) {
            $table->unsignedInteger('personal_discount')->nullable();
        });

        Schema::table(\App\Models\Lesson::TABLE, static function (Blueprint $table) {
            $table->uuid('price_id')->nullable();
            $table->foreign('price_id')
                ->references('id')
                ->on(\App\Models\Price::TABLE)
                ->onDelete('cascade');
        });

        Schema::table(\App\Models\Schedule::TABLE, static function (Blueprint $table) {
            $table->uuid('price_id')->nullable();
            $table->foreign('price_id')
                ->references('id')
                ->on(\App\Models\Price::TABLE)
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table(\App\Models\Schedule::TABLE, static function (Blueprint $table) {
            $table->dropForeign('schedules_price_id_foreign');
            $table->dropColumn('price_id');
        });

        Schema::table(\App\Models\Lesson::TABLE, static function (Blueprint $table) {
            $table->dropForeign('lessons_price_id_foreign');
            $table->dropColumn('price_id');
        });

        Schema::table(\App\Models\Student::TABLE, static function (Blueprint $table) {
            $table->dropColumn('personal_discount');
        });

        Schema::dropIfExists(\App\Models\Price::TABLE);
    }
};
