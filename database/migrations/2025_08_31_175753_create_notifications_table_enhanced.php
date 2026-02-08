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
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('title');
                $table->text('message');
                $table->enum('type', ['general', 'course', 'exam', 'assignment', 'grade', 'announcement', 'reminder', 'warning', 'system'])->default('general');
                $table->string('action_url')->nullable();
                $table->string('action_text')->nullable();
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
                $table->enum('target_type', ['all_students', 'course_students', 'year_students', 'subject_students', 'individual'])->default('individual');
                $table->unsignedBigInteger('target_id')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->json('data')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'is_read']);
                $table->index(['type', 'priority']);
                $table->index(['target_type', 'target_id']);
                $table->index(['created_at', 'expires_at']);
            });
        } else {
            // إضافة الحقول المفقودة إذا كان الجدول موجود
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'sender_id')) {
                    $table->foreignId('sender_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('notifications', 'action_url')) {
                    $table->string('action_url')->nullable()->after('message');
                }
                if (!Schema::hasColumn('notifications', 'action_text')) {
                    $table->string('action_text')->nullable()->after('action_url');
                }
                if (!Schema::hasColumn('notifications', 'priority')) {
                    $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('action_text');
                }
                if (!Schema::hasColumn('notifications', 'target_type')) {
                    $table->enum('target_type', ['all_students', 'course_students', 'year_students', 'subject_students', 'individual'])->default('individual')->after('priority');
                }
                if (!Schema::hasColumn('notifications', 'target_id')) {
                    $table->unsignedBigInteger('target_id')->nullable()->after('target_type');
                }
                if (!Schema::hasColumn('notifications', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('read_at');
                }
                if (!Schema::hasColumn('notifications', 'data')) {
                    $table->json('data')->nullable()->after('expires_at');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
