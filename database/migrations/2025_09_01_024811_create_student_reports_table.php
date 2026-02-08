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
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('report_month'); // YYYY-MM format
            $table->enum('report_type', ['monthly', 'weekly', 'custom'])->default('monthly');
            $table->json('report_data'); // بيانات التقرير الكاملة
            $table->enum('sent_via', ['whatsapp', 'email', 'sms'])->default('whatsapp');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['student_id', 'report_month']);
            $table->index(['parent_id', 'sent_at']);
            $table->unique(['student_id', 'report_month', 'report_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};
