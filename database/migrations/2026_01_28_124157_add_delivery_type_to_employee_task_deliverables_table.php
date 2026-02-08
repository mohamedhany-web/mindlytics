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
        Schema::table('employee_task_deliverables', function (Blueprint $table) {
            $table->enum('delivery_type', ['file', 'image', 'link'])->default('file')->after('description')->comment('نوع التسليم');
            $table->string('link_url')->nullable()->after('delivery_type')->comment('رابط التسليم (إذا كان النوع رابط)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_task_deliverables', function (Blueprint $table) {
            $table->dropColumn(['delivery_type', 'link_url']);
        });
    }
};
