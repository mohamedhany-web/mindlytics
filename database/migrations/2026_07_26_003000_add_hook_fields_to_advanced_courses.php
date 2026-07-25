<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('advanced_courses')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('advanced_courses', 'hook')) {
                $table->text('hook')->nullable()->after('description');
            }
            if (! Schema::hasColumn('advanced_courses', 'hook_en')) {
                $table->text('hook_en')->nullable()->after('description_en');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('advanced_courses')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            foreach (['hook', 'hook_en'] as $col) {
                if (Schema::hasColumn('advanced_courses', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
