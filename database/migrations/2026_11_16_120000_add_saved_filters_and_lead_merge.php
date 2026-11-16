<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * فلاتر CRM المحفوظة لكل مستخدم، وربط العميل المدموج بالعميل اللي اتدمج فيه (docs/SALES_CRM.md).
 * إضافات بس: جدول جديد وعمود nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crm_saved_filters')) {
            Schema::create('crm_saved_filters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('module', 40);
                $table->string('name', 80);
                $table->json('params');
                $table->timestamps();
                $table->unique(['user_id', 'module', 'name']);
            });
        }

        if (! Schema::hasColumn('sales_leads', 'merged_into_id')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                $table->unsignedBigInteger('merged_into_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sales_leads', 'merged_into_id')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                $table->dropIndex(['merged_into_id']);
                $table->dropColumn('merged_into_id');
            });
        }
        Schema::dropIfExists('crm_saved_filters');
    }
};
