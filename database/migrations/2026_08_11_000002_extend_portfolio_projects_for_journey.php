<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->string('program_type', 20)->nullable()->after('advanced_course_id'); // recorded | diploma
            $table->foreignId('offline_course_id')->nullable()->after('program_type')->constrained('offline_courses')->nullOnDelete();
            $table->boolean('is_capstone')->default(false)->after('project_type');
            $table->json('technologies')->nullable()->after('description');
            $table->text('what_i_learned')->nullable()->after('technologies');
            $table->text('challenges')->nullable()->after('what_i_learned');
            $table->unsignedTinyInteger('rubric_code_quality')->nullable()->after('instructor_notes');
            $table->unsignedTinyInteger('rubric_ui_ux')->nullable()->after('rubric_code_quality');
            $table->unsignedTinyInteger('rubric_functionality')->nullable()->after('rubric_ui_ux');
            $table->unsignedTinyInteger('rubric_problem_solving')->nullable()->after('rubric_functionality');
            $table->unsignedTinyInteger('rubric_documentation')->nullable()->after('rubric_problem_solving');
            $table->decimal('rubric_average', 4, 2)->nullable()->after('rubric_documentation');
            $table->unsignedInteger('revision_count')->default(0)->after('rejected_reason');
            $table->boolean('is_featured')->default(false)->after('is_visible');

            $table->index(['program_type', 'status', 'is_visible']);
            $table->index(['status', 'published_at']);
            $table->index('is_featured');
            $table->index('is_capstone');
        });

        DB::table('portfolio_projects')->whereNotNull('academic_year_id')->update(['program_type' => 'diploma']);
        DB::table('portfolio_projects')
            ->whereNull('academic_year_id')
            ->whereNotNull('advanced_course_id')
            ->update(['program_type' => 'recorded']);
    }

    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropIndex(['portfolio_projects_program_type_status_is_visible_index']);
            $table->dropIndex(['portfolio_projects_status_published_at_index']);
            $table->dropIndex(['portfolio_projects_is_featured_index']);
            $table->dropIndex(['portfolio_projects_is_capstone_index']);
            $table->dropConstrainedForeignId('offline_course_id');
            $table->dropColumn([
                'program_type',
                'is_capstone',
                'technologies',
                'what_i_learned',
                'challenges',
                'rubric_code_quality',
                'rubric_ui_ux',
                'rubric_functionality',
                'rubric_problem_solving',
                'rubric_documentation',
                'rubric_average',
                'revision_count',
                'is_featured',
            ]);
        });
    }
};
