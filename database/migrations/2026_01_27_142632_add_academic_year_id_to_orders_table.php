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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->after('advanced_course_id')->constrained('academic_years')->onDelete('cascade')->comment('ID المسار التعليمي');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
        });
    }
};
