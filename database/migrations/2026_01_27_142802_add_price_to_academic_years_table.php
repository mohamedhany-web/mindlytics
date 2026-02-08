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
        Schema::table('academic_years', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_years', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('description')->comment('سعر المسار التعليمي');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            if (Schema::hasColumn('academic_years', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
};
