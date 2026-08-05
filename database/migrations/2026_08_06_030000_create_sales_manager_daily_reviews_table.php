<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_manager_daily_reviews')) {
            return;
        }

        Schema::create('sales_manager_daily_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_team_id')->nullable()->constrained('sales_teams')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
            $table->date('work_date');
            $table->string('status', 20)->default('draft'); // draft|reviewed|approved
            $table->decimal('verified_score', 5, 1)->nullable();
            $table->json('score_snapshot')->nullable();
            $table->string('recommendation', 40)->default('none');
            $table->decimal('proposed_deduction_amount', 10, 2)->nullable();
            $table->text('manager_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'work_date'], 'sm_daily_reviews_employee_date_uq');
            $table->index(['sales_team_id', 'work_date']);
            $table->index(['manager_id', 'work_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_manager_daily_reviews');
    }
};
