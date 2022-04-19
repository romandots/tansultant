<?php
/**
 * File: 2019_07_30_042248_create_branches_table.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateBranchesTable
 */
class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('branches', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name')->index();
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->text('phone')->nullable();
            $table->text('email')->nullable();
            $table->text('url')->nullable();
            $table->text('vk_url')->nullable();
            $table->text('facebook_url')->nullable();
            $table->text('telegram_username')->nullable();
            $table->text('instagram_username')->nullable();
            $table->json('address')->nullable();
            $table->integer('number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('schedules', static function (Blueprint $table) {
            $table->foreign('branch_id')
                ->references('id')
                ->on(\App\Models\Branch::TABLE)
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
        Schema::table('schedules', static function (Blueprint $table) {
            $table->dropForeign('schedules_branch_id_foreign');
        });
        Schema::dropIfExists('branches');
    }
}
