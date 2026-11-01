<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * أنشطة مخططة في CRM (مكالمات، اجتماعات، مهام) — زي Calls/Meetings/Tasks في SuiteCRM.
 * جدول جديد فقط؛ لا يغيّر sales_leads ولا sales_activities (السجل اللي حصل يفضل في sales_activities).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_planned_activities')) {
            return;
        }

        Schema::create('sales_planned_activities', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 16)->default('call');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
            $table->foreignId('student_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('starts_at');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('priority', 16)->default('normal');
            $table->string('status', 16)->default('planned');
            $table->string('outcome', 32)->nullable();
            $table->text('result_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sales_activity_id')->nullable()->constrained('sales_activities')->nullOnDelete();
            $table->timestamps();

            $table->index(['assigned_to', 'status', 'starts_at']);
            $table->index(['status', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_planned_activities');
    }
};
