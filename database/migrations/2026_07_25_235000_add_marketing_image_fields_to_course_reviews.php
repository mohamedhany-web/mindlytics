<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_reviews')) {
            return;
        }

        Schema::table('course_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('course_reviews', 'comment')) {
                $table->text('comment')->nullable()->after('review');
            }
            if (! Schema::hasColumn('course_reviews', 'status')) {
                $table->string('status', 20)->nullable()->after('is_approved');
            }
            if (! Schema::hasColumn('course_reviews', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_approved');
            }
            if (! Schema::hasColumn('course_reviews', 'is_verified_purchase')) {
                $table->boolean('is_verified_purchase')->default(false)->after('is_approved');
            }
            if (! Schema::hasColumn('course_reviews', 'helpful_count')) {
                $table->unsignedInteger('helpful_count')->default(0)->after('is_featured');
            }
            if (! Schema::hasColumn('course_reviews', 'reviewer_name')) {
                $table->string('reviewer_name', 120)->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('course_reviews', 'image_path')) {
                $table->string('image_path', 500)->nullable()->after('comment');
            }
            if (! Schema::hasColumn('course_reviews', 'is_marketing')) {
                $table->boolean('is_marketing')->default(false)->after('is_featured');
            }
        });

        // السماح بمراجعات تسويقية بدون مستخدم
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE course_reviews MODIFY user_id BIGINT UNSIGNED NULL');
        } catch (\Throwable $e) {
            // ignore if already nullable or engine differs
            report($e);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_reviews')) {
            return;
        }

        Schema::table('course_reviews', function (Blueprint $table) {
            foreach (['comment', 'status', 'is_featured', 'is_verified_purchase', 'helpful_count', 'reviewer_name', 'image_path', 'is_marketing'] as $col) {
                if (Schema::hasColumn('course_reviews', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
