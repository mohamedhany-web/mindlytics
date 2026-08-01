<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_day_blocks')) {
            Schema::create('sales_day_blocks', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->time('start_time');
                $table->time('end_time');
                $table->string('activity_type', 32);
                $table->string('goal_text')->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'sort_order']);
            });
        }

        if (Schema::hasTable('sales_day_blocks') && DB::table('sales_day_blocks')->count() === 0) {
            $now = now();
            $rows = [
                ['name' => 'Morning Brief', 'code' => 'morning_brief', 'start_time' => '09:00:00', 'end_time' => '09:20:00', 'activity_type' => 'brief', 'goal_text' => 'مراجعة أرقام أمس، الهدف اليوم، الاعتراضات الشائعة', 'sort_order' => 10],
                ['name' => 'Call Block #1', 'code' => 'call_block_1', 'start_time' => '09:20:00', 'end_time' => '11:00:00', 'activity_type' => 'calls', 'goal_text' => 'اتصالات بدون أي مقاطعات', 'sort_order' => 20],
                ['name' => 'Break', 'code' => 'break_1', 'start_time' => '11:00:00', 'end_time' => '11:15:00', 'activity_type' => 'break', 'goal_text' => 'راحة قصيرة', 'sort_order' => 30],
                ['name' => 'Follow-up Block', 'code' => 'followup_block', 'start_time' => '11:15:00', 'end_time' => '13:00:00', 'activity_type' => 'followup', 'goal_text' => 'متابعة العملاء القدامى وإغلاق الصفقات', 'sort_order' => 40],
                ['name' => 'Lunch', 'code' => 'lunch', 'start_time' => '13:00:00', 'end_time' => '14:00:00', 'activity_type' => 'lunch', 'goal_text' => 'استراحة غداء', 'sort_order' => 50],
                ['name' => 'Call Block #2', 'code' => 'call_block_2', 'start_time' => '14:00:00', 'end_time' => '15:30:00', 'activity_type' => 'calls', 'goal_text' => 'اتصالات جديدة', 'sort_order' => 60],
                ['name' => 'Break', 'code' => 'break_2', 'start_time' => '15:30:00', 'end_time' => '15:45:00', 'activity_type' => 'break', 'goal_text' => 'راحة قصيرة', 'sort_order' => 70],
                ['name' => 'WhatsApp + Closing', 'code' => 'whatsapp_closing', 'start_time' => '15:45:00', 'end_time' => '17:00:00', 'activity_type' => 'whatsapp_closing', 'goal_text' => 'رسائل - عروض - حجز - تأكيد الدفع', 'sort_order' => 80],
                ['name' => 'Daily Report', 'code' => 'daily_report', 'start_time' => '17:00:00', 'end_time' => '17:20:00', 'activity_type' => 'report', 'goal_text' => 'رفع التقرير ومراجعة النتائج', 'sort_order' => 90],
            ];

            foreach ($rows as $row) {
                DB::table('sales_day_blocks')->insert(array_merge($row, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_day_blocks');
    }
};
