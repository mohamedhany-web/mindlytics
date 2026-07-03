<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->enum('plan_type', ['equity', 'revenue_share', 'partnership', 'fixed_return', 'strategic'])->default('partnership');
            $table->decimal('min_investment', 14, 2)->default(0);
            $table->decimal('max_investment', 14, 2)->nullable();
            $table->decimal('target_amount', 14, 2)->nullable();
            $table->string('currency', 3)->default('EGP');
            $table->unsignedSmallInteger('duration_months')->nullable();
            $table->decimal('expected_return_min', 5, 2)->nullable();
            $table->decimal('expected_return_max', 5, 2)->nullable();
            $table->enum('return_model', ['profit_share', 'fixed_annual', 'equity_stake', 'revenue_share', 'custom'])->default('custom');
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->text('eligibility_criteria')->nullable();
            $table->text('benefits')->nullable();
            $table->text('terms_summary')->nullable();
            $table->text('legal_notes')->nullable();
            $table->json('process_steps')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index('plan_type');
        });

        Schema::create('investment_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_plan_id')->nullable()->constrained('investment_plans')->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('country_code', 5)->nullable();
            $table->string('company_name')->nullable();
            $table->enum('investor_type', ['individual', 'company', 'fund'])->default('individual');
            $table->decimal('proposed_amount', 14, 2)->nullable();
            $table->string('currency', 3)->default('EGP');
            $table->text('experience_notes')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', [
                'pending',
                'under_review',
                'meeting_scheduled',
                'approved',
                'rejected',
                'withdrawn',
            ])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('email');
        });

        Schema::create('investment_policies', function (Blueprint $table) {
            $table->id();
            $table->text('overview')->nullable();
            $table->text('eligibility_rules')->nullable();
            $table->text('legal_framework')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('privacy_notice')->nullable();
            $table->text('process_description')->nullable();
            $table->text('disclaimer')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_inquiries');
        Schema::dropIfExists('investment_plans');
        Schema::dropIfExists('investment_policies');
    }
};
