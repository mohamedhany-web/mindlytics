<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('scholarship_groups')) {
            Schema::create('scholarship_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('scholarship_program_id')->constrained('scholarship_programs')->cascadeOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('scholarship_group_members')) {
            Schema::create('scholarship_group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('scholarship_group_id')->constrained('scholarship_groups')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['scholarship_group_id', 'user_id'], 'sg_members_unique');
            });
        }

        if (! Schema::hasTable('course_section_visible_groups')) {
            Schema::create('course_section_visible_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_section_id')->constrained('course_sections')->cascadeOnDelete();
                $table->foreignId('scholarship_group_id')->constrained('scholarship_groups')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['course_section_id', 'scholarship_group_id'], 'cs_visible_groups_unique');
            });
        }

        if (! Schema::hasTable('curriculum_item_visible_groups')) {
            Schema::create('curriculum_item_visible_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curriculum_item_id')->constrained('curriculum_items')->cascadeOnDelete();
                $table->foreignId('scholarship_group_id')->constrained('scholarship_groups')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['curriculum_item_id', 'scholarship_group_id'], 'ci_visible_groups_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_item_visible_groups');
        Schema::dropIfExists('course_section_visible_groups');
        Schema::dropIfExists('scholarship_group_members');
        Schema::dropIfExists('scholarship_groups');
    }
};
