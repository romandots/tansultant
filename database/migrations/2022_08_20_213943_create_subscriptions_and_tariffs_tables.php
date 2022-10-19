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
        Schema::create('tariffs', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name')->index();
            $table->float('price');
            $table->float('prolongation_price');
            $table->unsignedInteger('courses_limit')->nullable();
            $table->unsignedInteger('visits_limit')->nullable();
            $table->unsignedInteger('days_limit')->nullable();
            $table->unsignedInteger('holds_limit')->nullable();
            $table->text('status');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('subscriptions', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name')->index();
            $table->uuid('tariff_id');
            $table->uuid('student_id');
            $table->text('status');
            $table->unsignedInteger('courses_limit')->nullable();
            $table->unsignedInteger('visits_limit')->nullable();
            $table->unsignedInteger('days_limit')->nullable();
            $table->unsignedInteger('holds_limit')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->foreign('tariff_id')
                ->references('id')
                ->on('tariffs')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
        });

        \convertPostgresColumnTextToEnum('subscriptions', 'status', \App\Models\Enum\SubscriptionStatus::cases());
        \convertPostgresColumnTextToEnum('tariffs', 'status', \App\Models\Enum\TariffStatus::cases());

        Schema::create('holds', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subscription_id');
            $table->timestamp('created_at');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();

            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
                ->onDelete('cascade');
        });

        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->uuid('hold_id')->nullable();
            $table->foreign('hold_id')
                ->references('id')
                ->on('holds')
                ->onDelete('cascade');
        });

        Schema::create('tariff_has_courses', static function (Blueprint $table) {
            $table->uuid('tariff_id')->index();
            $table->uuid('course_id')->index();
            $table->timestamp('created_at');

            $table->foreign('tariff_id')
                ->references('id')
                ->on('tariffs')
                ->onDelete('cascade');

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');

            $table->primary(['tariff_id', 'course_id'],
                'tariff_has_courses_primary');
        });

        Schema::create('subscription_has_courses', static function (Blueprint $table) {
            $table->uuid('subscription_id')->index();
            $table->uuid('course_id')->index();
            $table->timestamp('created_at');

            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
                ->onDelete('cascade');

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');

            $table->primary(['subscription_id', 'course_id'],
                'subscription_has_courses_primary');
        });

        Schema::table('visits', static function (Blueprint $table) {
            $table->uuid('subscription_id')->nullable();
            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
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
        Schema::table('visits', static function (Blueprint $table) {
            $table->dropColumn('subscription_id');
        });

        \DB::unprepared('DROP TYPE subscriptions_status CASCADE');
        \DB::unprepared('DROP TYPE tariffs_status CASCADE');

        Schema::dropIfExists('subscription_has_courses');
        Schema::dropIfExists('tariff_has_courses');

        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->dropForeign('subscriptions_hold_id_foreign');
            $table->dropColumn(['hold_id']);
        });

        Schema::dropIfExists('holds');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('tariffs');
    }
};
