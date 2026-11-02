<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ربط حجوزات الإرشاد بالـ CRM (docs/ADVISING.md): العميل المحتمل، والاجتماع المخطط في كاليندر الموظف.
 * عمودين nullable بس.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advising_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('advising_bookings', 'sales_lead_id')) {
                $table->foreignId('sales_lead_id')->nullable()->after('user_id')->constrained('sales_leads')->nullOnDelete();
            }
            if (! Schema::hasColumn('advising_bookings', 'sales_planned_activity_id')) {
                $table->foreignId('sales_planned_activity_id')->nullable()->after('sales_lead_id')->constrained('sales_planned_activities')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('advising_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('advising_bookings', 'sales_planned_activity_id')) {
                $table->dropConstrainedForeignId('sales_planned_activity_id');
            }
            if (Schema::hasColumn('advising_bookings', 'sales_lead_id')) {
                $table->dropConstrainedForeignId('sales_lead_id');
            }
        });
    }
};
