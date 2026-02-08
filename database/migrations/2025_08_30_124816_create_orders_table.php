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
        // التحقق من عدم وجود الجدول مسبقاً
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('advanced_course_id');
                $table->decimal('amount', 10, 2);
                $table->enum('payment_method', ['bank_transfer', 'cash', 'other'])->default('bank_transfer');
                $table->string('payment_proof')->nullable(); // صورة الإيصال
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamps();
                
                // سنضيف foreign keys لاحقاً عندما تكون الجداول جاهزة
                $table->index(['user_id', 'advanced_course_id']);
                $table->index(['status', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};