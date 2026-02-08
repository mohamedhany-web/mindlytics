<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('questions')) {
            return;
        }

        // Check if question_categories table exists before adding foreign key
        $hasCategoriesTable = Schema::hasTable('question_categories');

        Schema::table('questions', function (Blueprint $table) use ($hasCategoriesTable) {
            // إضافة الحقول الجديدة
            if ($hasCategoriesTable && !Schema::hasColumn('questions', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('question_bank_id')->constrained('question_categories')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('questions', 'difficulty_level')) {
                $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium')->after('points');
            }
            if (!Schema::hasColumn('questions', 'image_url')) {
                $table->string('image_url')->nullable()->after('difficulty_level');
            }
            if (!Schema::hasColumn('questions', 'audio_url')) {
                $table->string('audio_url')->nullable()->after('image_url');
            }
            if (!Schema::hasColumn('questions', 'video_url')) {
                $table->string('video_url')->nullable()->after('audio_url');
            }
            if (!Schema::hasColumn('questions', 'time_limit')) {
                $table->integer('time_limit')->nullable()->after('video_url'); // بالثواني
            }
            if (!Schema::hasColumn('questions', 'tags')) {
                $table->json('tags')->nullable()->after('time_limit');
            }
        });

        // Skip column modifications for SQLite
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        
        Schema::table('questions', function (Blueprint $table) use ($hasCategoriesTable) {
            // تحديث الحقول الموجودة
            if (Schema::hasColumn('questions', 'points')) {
                $table->decimal('points', 5, 2)->default(1.00)->change();
            }
            if (Schema::hasColumn('questions', 'correct_answer')) {
                $table->json('correct_answer')->change();
            }
            
            // إضافة فهارس فقط إذا كانت الأعمدة موجودة
            if ($hasCategoriesTable && Schema::hasColumn('questions', 'category_id') && Schema::hasColumn('questions', 'type')) {
                try {
                    $table->index(['category_id', 'type'], 'questions_category_id_type_index');
                } catch (\Exception $e) {
                    // Index موجود بالفعل
                }
            }
            if (Schema::hasColumn('questions', 'difficulty_level') && Schema::hasColumn('questions', 'is_active')) {
                try {
                    $table->index(['difficulty_level', 'is_active'], 'questions_difficulty_level_is_active_index');
                } catch (\Exception $e) {
                    // Index موجود بالفعل
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('questions')) {
            return;
        }

        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'category_id')) {
                $table->dropForeign(['category_id']);
            }
            $table->dropColumn([
                'category_id', 'difficulty_level', 'image_url', 
                'audio_url', 'video_url', 'time_limit', 'tags'
            ]);
        });
    }
};
