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
        if (Schema::hasTable('question_categories')) {
        Schema::table('question_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('question_categories', 'parent_id')) {
            $table->foreignId('parent_id')->nullable()->after('academic_subject_id')->constrained('question_categories')->onDelete('cascade');
            $table->index(['parent_id', 'order']);
                }
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id', 'order']);
            $table->dropColumn('parent_id');
        });
    }
};
