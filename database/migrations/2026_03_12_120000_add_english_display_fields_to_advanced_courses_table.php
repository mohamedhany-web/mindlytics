<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * حقول اختيارية للعرض عند اختيار واجهة الإنجليزية (بدون ترجمة آلية من طرف ثالث).
     */
    public function up(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('advanced_courses', 'title_en')) {
                $table->string('title_en')->nullable()->after('title');
            }
            if (! Schema::hasColumn('advanced_courses', 'description_en')) {
                $table->text('description_en')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (Schema::hasColumn('advanced_courses', 'description_en')) {
                $table->dropColumn('description_en');
            }
            if (Schema::hasColumn('advanced_courses', 'title_en')) {
                $table->dropColumn('title_en');
            }
        });
    }
};
