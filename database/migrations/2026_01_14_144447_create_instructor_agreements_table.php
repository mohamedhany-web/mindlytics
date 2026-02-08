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
        Schema::create('instructor_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('agreement_number')->unique();
            $table->enum('type', ['course_price', 'hourly_rate', 'monthly_salary'])->default('course_price');
            $table->decimal('rate', 12, 2); // السعر حسب النوع
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'active', 'suspended', 'terminated', 'completed'])->default('draft');
            $table->text('terms')->nullable(); // شروط العقد
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['instructor_id', 'status']);
            $table->index('agreement_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_agreements');
    }
};
