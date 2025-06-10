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
        Schema::create('formulas', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('equation')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::table('courses', static function (Blueprint $table){
            $table->uuid('formula_id')->nullable()->index();
            $table->foreign('formula_id')
                ->references('id')
                ->on(\App\Models\Formula::TABLE)
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('courses', static function (Blueprint $table){
            $table->dropForeign('courses_formula_id_foreign');
            $table->dropColumn('formula_id');
        });
        Schema::dropIfExists('formulas');
    }
};
