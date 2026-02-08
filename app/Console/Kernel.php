<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // إرسال التقارير الشهرية في أول يوم من كل شهر
        $schedule->command('reports:send-monthly')
                 ->monthlyOn(1, '09:00')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->emailOutputOnFailure(config('mail.admin_email'));

        // تنظيف الرسائل القديمة (أكثر من 6 أشهر)
        $schedule->call(function () {
            \App\Models\WhatsAppMessage::where('created_at', '<', now()->subMonths(6))->delete();
            \App\Models\ActivityLog::where('created_at', '<', now()->subMonths(3))->delete();
        })->monthly()->withoutOverlapping();

        // تحديث إحصائيات المنصة يومياً
        $schedule->call(function () {
            // تحديث إحصائيات المستخدمين النشطين
            cache()->remember('active_users_today', 3600, function () {
                return \App\Models\ActivityLog::whereDate('created_at', today())
                    ->distinct('user_id')
                    ->count();
            });
        })->daily();

        // معالجة حالات الأقساط وإرسال التذكيرات يومياً
        $schedule->command('installments:process')
                 ->dailyAt('08:00')
                 ->runInBackground()
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
