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
        if (!Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->text('features')->nullable(); // JSON array of features
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('original_price', 10, 2)->nullable(); // السعر الأصلي قبل الخصم
                $table->string('thumbnail')->nullable();
                $table->integer('duration_days')->nullable(); // مدة صلاحية الباقة بالأيام
                $table->integer('courses_count')->default(0); // عدد الكورسات في الباقة
                $table->integer('order')->default(0); // ترتيب العرض
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_popular')->default(false); // باقة شائعة
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('ends_at')->nullable();
                $table->timestamps();
                
                $table->index(['is_active', 'is_featured']);
                $table->index('slug');
            });
        }

        // جدول الربط بين الباقات والكورسات (many-to-many)
        if (!Schema::hasTable('package_course')) {
            Schema::create('package_course', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
                $table->foreignId('course_id')->constrained('advanced_courses')->onDelete('cascade');
                $table->integer('order')->default(0); // ترتيب الكورس في الباقة
                $table->timestamps();
                
                $table->unique(['package_id', 'course_id']);
                $table->index('package_id');
                $table->index('course_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_course');
        Schema::dropIfExists('packages');
    }
};

