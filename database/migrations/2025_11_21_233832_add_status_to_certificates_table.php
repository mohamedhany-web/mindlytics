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
        // التحقق من وجود الجدول أولاً
        if (!Schema::hasTable('certificates')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            // إضافة الأعمدة المفقودة بناءً على البنية الفعلية للجدول
            
            // إضافة course_name إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'course_name')) {
                $table->string('course_name')->nullable()->after('course_id');
            }
            
            // إضافة certificate_type إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'certificate_type')) {
                $table->enum('certificate_type', ['completion', 'achievement', 'participation', 'certification'])->default('completion')->after('course_name');
            }
            
            // إضافة issue_date إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'issue_date')) {
                $table->date('issue_date')->nullable()->after('certificate_type');
            }
            
            // إضافة expiry_date إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('issue_date');
            }
            
            // إضافة pdf_path إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('template');
            }
            
            // إضافة verification_code إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'verification_code')) {
                $table->string('verification_code')->nullable()->unique()->after('pdf_path');
            }
            
            // إضافة metadata إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'metadata')) {
                $table->text('metadata')->nullable()->after('verification_code');
            }
            
            // إضافة is_verified إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'is_verified')) {
                $table->boolean('is_verified')->default(true)->after('metadata');
            }
            
            // إضافة is_public إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('is_verified');
            }
            
            // إضافة status
            if (!Schema::hasColumn('certificates', 'status')) {
                $table->enum('status', ['pending', 'issued', 'revoked'])->default('pending')->after('is_verified');
            }
            
            // إضافة title إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'title')) {
                $table->string('title')->nullable()->after('course_name');
            }
            
            // إضافة description إذا لم يكن موجوداً
            if (!Schema::hasColumn('certificates', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('certificates', 'is_public')) {
                $table->dropColumn('is_public');
            }
            if (Schema::hasColumn('certificates', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
            if (Schema::hasColumn('certificates', 'metadata')) {
                $table->dropColumn('metadata');
            }
            if (Schema::hasColumn('certificates', 'verification_code')) {
                $table->dropColumn('verification_code');
            }
            if (Schema::hasColumn('certificates', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }
            if (Schema::hasColumn('certificates', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
            if (Schema::hasColumn('certificates', 'issue_date')) {
                $table->dropColumn('issue_date');
            }
            if (Schema::hasColumn('certificates', 'certificate_type')) {
                $table->dropColumn('certificate_type');
            }
            if (Schema::hasColumn('certificates', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('certificates', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('certificates', 'course_name')) {
                $table->dropColumn('course_name');
            }
        });
    }
};
