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
        if (!Schema::hasTable('learning_path_enrollments')) {
            Schema::create('learning_path_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID الطالب');
                $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade')->comment('ID المسار التعليمي');
                $table->timestamp('enrolled_at')->useCurrent()->comment('تاريخ التسجيل');
                $table->timestamp('activated_at')->nullable()->comment('تاريخ التفعيل');
                $table->foreignId('activated_by')->nullable()->constrained('users')->onDelete('set null')->comment('مفعل بواسطة');
                $table->enum('status', ['pending', 'active', 'completed', 'suspended'])->default('pending')->comment('حالة التسجيل');
                $table->decimal('progress', 5, 2)->default(0)->comment('نسبة التقدم في المسار');
                $table->text('notes')->nullable()->comment('ملاحظات إدارية');
                $table->timestamps();
                
                $table->unique(['user_id', 'academic_year_id'], 'unique_user_learning_path');
                $table->index(['status', 'enrolled_at']);
                $table->index('academic_year_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_path_enrollments');
    }
};
