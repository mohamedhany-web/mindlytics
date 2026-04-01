<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderator_mkt_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('goals')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->unsignedBigInteger('design_task_cycle_id')->nullable();
            $table->foreign('design_task_cycle_id', 'fk_mkt_plans_dcycle')
                ->references('id')
                ->on('design_task_cycles')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['moderator_id', 'status'], 'idx_mkt_plans_mod_stat');
        });

        Schema::create('moderator_mkt_platforms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id', 'fk_mkt_plat_plan')
                ->references('id')
                ->on('moderator_mkt_plans')
                ->cascadeOnDelete();
            $table->string('platform_key', 40);
            $table->string('custom_label', 120)->nullable();
            $table->string('profile_url', 500)->nullable();
            $table->text('strategy_notes')->nullable();
            $table->text('cadence_notes')->nullable();
            $table->string('color_hex', 7)->default('#6366f1');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('plan_id', 'idx_mkt_plat_plan');
        });

        Schema::create('moderator_mkt_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id', 'fk_mkt_ce_plan')
                ->references('id')
                ->on('moderator_mkt_plans')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('platform_id')->nullable();
            $table->foreign('platform_id', 'fk_mkt_ce_plat')
                ->references('id')
                ->on('moderator_mkt_platforms')
                ->nullOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->unsignedBigInteger('design_task_cycle_id')->nullable();
            $table->foreign('design_task_cycle_id', 'fk_mkt_ce_dcycle')
                ->references('id')
                ->on('design_task_cycles')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['plan_id', 'starts_at'], 'idx_mkt_ce_plan_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderator_mkt_calendar_events');
        Schema::dropIfExists('moderator_mkt_platforms');
        Schema::dropIfExists('moderator_mkt_plans');
    }
};
