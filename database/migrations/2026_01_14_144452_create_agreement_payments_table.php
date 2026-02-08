<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agreement_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agreement_id');
            $table->foreign('agreement_id')->references('id')->on('instructor_agreements')->onDelete('cascade');
            $table->unsignedBigInteger('instructor_id');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('payment_number')->unique();
            $table->enum('type', ['course_completion', 'hourly_teaching', 'monthly_salary', 'bonus', 'other'])->default('course_completion');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('related_course_id')->nullable();
            $table->foreign('related_course_id')->references('id')->on('advanced_courses')->onDelete('set null');
            $table->unsignedBigInteger('related_lecture_id')->nullable();
            $table->foreign('related_lecture_id')->references('id')->on('lectures')->onDelete('set null');
            $table->integer('hours_count')->nullable(); // عدد الساعات في حالة hourly_rate
            $table->date('payment_date')->nullable(); // تاريخ الاستحقاق
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['agreement_id', 'status']);
            $table->index(['instructor_id', 'status']);
            $table->index('payment_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_payments');
    }
};
