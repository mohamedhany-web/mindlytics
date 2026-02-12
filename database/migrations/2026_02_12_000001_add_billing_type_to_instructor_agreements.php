<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * نوع الاتفاقية: بالجلسة | راتب شهري | باكورس كامل
     */
    public function up(): void
    {
        Schema::table('instructor_agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('instructor_agreements', 'billing_type')) {
                $table->string('billing_type', 32)->default('per_session')->after('offline_course_id')
                    ->comment('per_session=بالجلسة, monthly=راتب شهري, full_course=باكورس كامل');
            }
            if (!Schema::hasColumn('instructor_agreements', 'monthly_amount')) {
                $table->decimal('monthly_amount', 10, 2)->nullable()->after('total_amount')
                    ->comment('الراتب الشهري عند النوع راتب شهري');
            }
            if (!Schema::hasColumn('instructor_agreements', 'months_count')) {
                $table->unsignedSmallInteger('months_count')->nullable()->after('monthly_amount')
                    ->comment('عدد الأشهر عند النوع راتب شهري');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_agreements', function (Blueprint $table) {
            if (Schema::hasColumn('instructor_agreements', 'months_count')) {
                $table->dropColumn('months_count');
            }
            if (Schema::hasColumn('instructor_agreements', 'monthly_amount')) {
                $table->dropColumn('monthly_amount');
            }
            if (Schema::hasColumn('instructor_agreements', 'billing_type')) {
                $table->dropColumn('billing_type');
            }
        });
    }
};
