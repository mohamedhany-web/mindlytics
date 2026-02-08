<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول المدونة
        if (!Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('excerpt')->nullable();
                $table->longText('content');
                $table->string('featured_image')->nullable();
                $table->json('images')->nullable(); // صور إضافية
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->boolean('is_featured')->default(false);
                $table->integer('views_count')->default(0);
                $table->json('tags')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->dateTime('published_at')->nullable();
                $table->timestamps();
                
                $table->index(['status', 'published_at']);
                $table->index('is_featured');
                $table->index('slug');
            });
        }

        // جدول الأسئلة الشائعة
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->nullable(); // عام، تقني، مالي، إلخ
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->integer('views_count')->default(0);
                $table->boolean('is_featured')->default(false);
                $table->timestamps();
                
                $table->index(['is_active', 'order']);
                $table->index('category');
            });
        }

        // جدول رسائل التواصل
        if (!Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('subject');
                $table->text('message');
                $table->enum('status', ['new', 'read', 'replied', 'archived'])->default('new');
                $table->text('admin_notes')->nullable();
                $table->foreignId('replied_by')->nullable()->constrained('users')->onDelete('set null');
                $table->dateTime('replied_at')->nullable();
                $table->timestamps();
                
                $table->index(['status', 'created_at']);
            });
        }

        // جدول معرض الصور والفيديوهات
        if (!Schema::hasTable('media_galleries')) {
            Schema::create('media_galleries', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('type', ['image', 'video', 'document'])->default('image');
                $table->string('file_path');
                $table->string('thumbnail_path')->nullable(); // للفيديوهات
                $table->string('file_name');
                $table->string('mime_type');
                $table->bigInteger('file_size'); // بالبايت
                $table->string('category')->nullable(); // events, achievements, courses, etc
                $table->json('tags')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('views_count')->default(0);
                $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
                $table->timestamps();
                
                $table->index(['type', 'is_active']);
                $table->index(['category', 'is_featured']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('media_galleries');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('blog_posts');
    }
};
