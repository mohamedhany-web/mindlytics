<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assignments') && !Schema::hasColumn('assignments', 'group_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->foreignId('group_id')->nullable()->after('advanced_course_id')->constrained('groups')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('assignment_submissions') && !Schema::hasColumn('assignment_submissions', 'group_id')) {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                $table->foreignId('group_id')->nullable()->after('assignment_id')->constrained('groups')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('assignment_submissions') && Schema::hasColumn('assignment_submissions', 'group_id')) {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                $table->dropForeign(['group_id']);
            });
        }
        if (Schema::hasTable('assignments') && Schema::hasColumn('assignments', 'group_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropForeign(['group_id']);
            });
        }
    }
};
