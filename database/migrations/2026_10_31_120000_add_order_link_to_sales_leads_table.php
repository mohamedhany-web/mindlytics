<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ربط الـ Lead بالطلب الحقيقي (docs/SALES_INTEGRITY.md):
 * الفوز والعمولة يعتمدان على فلوس دخلت فعلاً، وتُسترجع العمولة عند الاسترداد.
 * أعمدة جديدة nullable فقط — لا تعديل على أي صف موجود، ولا foreign keys حتى لا يُقفل الجدول على الإنتاج.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_leads', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->index();
            }
            if (! Schema::hasColumn('sales_leads', 'student_user_id')) {
                $table->unsignedBigInteger('student_user_id')->nullable()->index();
            }
            if (! Schema::hasColumn('sales_leads', 'order_linked_at')) {
                $table->timestamp('order_linked_at')->nullable();
            }
            if (! Schema::hasColumn('sales_leads', 'won_override_reason')) {
                $table->text('won_override_reason')->nullable();
            }
            if (! Schema::hasColumn('sales_leads', 'commission_reversed_amount')) {
                $table->decimal('commission_reversed_amount', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('sales_leads', 'commission_reversed_at')) {
                $table->timestamp('commission_reversed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            foreach (['order_id', 'student_user_id'] as $column) {
                if (Schema::hasColumn('sales_leads', $column)) {
                    $table->dropIndex([$column]);
                }
            }
            $columns = array_values(array_filter(
                ['order_id', 'student_user_id', 'order_linked_at', 'won_override_reason', 'commission_reversed_amount', 'commission_reversed_at'],
                fn ($c) => Schema::hasColumn('sales_leads', $c)
            ));
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
