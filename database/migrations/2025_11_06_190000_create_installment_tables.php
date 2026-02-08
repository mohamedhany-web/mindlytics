<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('installment_plans')) {
            Schema::create('installment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('advanced_course_id')->nullable()->constrained('advanced_courses')->nullOnDelete();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->unsignedInteger('installments_count');
            $table->string('frequency_unit')->default('month');
            $table->unsignedInteger('frequency_interval')->default(1);
            $table->unsignedInteger('grace_period_days')->default(0);
            $table->boolean('auto_generate_on_enrollment')->default(false);
            $table->boolean('is_active')->default(true);
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('installment_agreements')) {
            Schema::create('installment_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_plan_id')->constrained('installment_plans')->cascadeOnDelete();
            $table->foreignId('student_course_enrollment_id')->constrained('student_course_enrollments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('advanced_course_id')->nullable()->constrained('advanced_courses')->nullOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->unsignedInteger('installments_count');
            $table->date('start_date');
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('installment_payments')) {
            Schema::create('installment_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_agreement_id')->constrained('installment_agreements')->cascadeOnDelete();
            $table->unsignedInteger('sequence_number');
            $table->date('due_date');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['installment_agreement_id', 'sequence_number'], 'installment_payments_agreement_seq_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_payments');
        Schema::dropIfExists('installment_agreements');
        Schema::dropIfExists('installment_plans');
    }
};
