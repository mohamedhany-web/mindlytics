<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * يدعم ترتيب الخلاصة: ORDER BY is_pinned DESC, id DESC مع مرشح course_id.
     */
    public function up(): void
    {
        Schema::table('course_community_posts', function (Blueprint $table) {
            $table->index(['course_id', 'is_pinned', 'id'], 'course_community_posts_course_pinned_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('course_community_posts', function (Blueprint $table) {
            $table->dropIndex('course_community_posts_course_pinned_id_idx');
        });
    }
};
