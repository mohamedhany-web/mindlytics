<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_cycle_moderator_planner_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('design_task_cycle_id');
            $table->foreign('design_task_cycle_id', 'fk_dc_mod_planner_cycle')
                ->references('id')
                ->on('design_task_cycles')
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('department', 120)->nullable()->comment('قسم أو جهة التوجيه');
            $table->string('time_slot', 80)->nullable()->comment('فترة اليوم: صباح، ظهر، ...');
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['design_task_cycle_id', 'due_date'], 'idx_dc_mod_planner_cyc_due');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_cycle_moderator_planner_items');
    }
};
