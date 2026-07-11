<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ظهور القسم/العنصر لطلبة منحة مختارين (موديول المنح فقط).
     * visibility_scope=all → كل الطلبة المسجلين
     * visibility_scope=selected → فقط الطلبة في جداول الربط
     */
    public function up(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('course_sections', 'visibility_scope')) {
                $table->string('visibility_scope', 16)->default('all')->after('unlock_percent');
            }
        });

        Schema::table('curriculum_items', function (Blueprint $table) {
            if (! Schema::hasColumn('curriculum_items', 'visibility_scope')) {
                $table->string('visibility_scope', 16)->default('all')->after('is_active');
            }
        });

        if (! Schema::hasTable('course_section_visible_students')) {
            Schema::create('course_section_visible_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_section_id')->constrained('course_sections')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['course_section_id', 'user_id'], 'cs_visible_students_unique');
            });
        }

        if (! Schema::hasTable('curriculum_item_visible_students')) {
            Schema::create('curriculum_item_visible_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curriculum_item_id')->constrained('curriculum_items')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['curriculum_item_id', 'user_id'], 'ci_visible_students_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_item_visible_students');
        Schema::dropIfExists('course_section_visible_students');

        if (Schema::hasColumn('curriculum_items', 'visibility_scope')) {
            Schema::table('curriculum_items', function (Blueprint $table) {
                $table->dropColumn('visibility_scope');
            });
        }

        if (Schema::hasColumn('course_sections', 'visibility_scope')) {
            Schema::table('course_sections', function (Blueprint $table) {
                $table->dropColumn('visibility_scope');
            });
        }
    }
};
