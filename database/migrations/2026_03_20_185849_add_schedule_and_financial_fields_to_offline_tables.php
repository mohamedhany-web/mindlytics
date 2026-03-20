<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_course_groups', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('class_time');
            $table->date('end_date')->nullable()->after('start_date');
            $table->decimal('session_duration_hours', 4, 1)->nullable()->after('end_date');
            $table->foreignId('location_id')->nullable()->after('location')
                  ->constrained('offline_locations')->onDelete('set null');
        });

        Schema::table('offline_course_enrollments', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->default(0)->after('notes');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('total_amount');
            $table->decimal('remaining_amount', 10, 2)->default(0)->after('paid_amount');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('remaining_amount');
            $table->foreignId('invoice_id')->nullable()->after('payment_status')
                  ->constrained('invoices')->onDelete('set null');
            $table->string('payment_method')->nullable()->after('invoice_id');
            $table->text('payment_notes')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('offline_course_groups', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['start_date', 'end_date', 'session_duration_hours', 'location_id']);
        });

        Schema::table('offline_course_enrollments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn([
                'total_amount', 'paid_amount', 'remaining_amount',
                'payment_status', 'invoice_id', 'payment_method', 'payment_notes'
            ]);
        });
    }
};
