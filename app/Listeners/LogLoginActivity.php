<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogLoginActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // تأجيل تسجيل النشاط لتجنب أي مشاكل
        try {
            $user = $event->user;
            
            $userRole = isset($user->role) ? $user->role : 'unknown';
            $userName = isset($user->name) ? $user->name : 'Unknown';
            $userAgent = request()->userAgent();
            $userAgent = $userAgent ? $userAgent : '';
            $fullUrl = request()->fullUrl();
            $fullUrl = $fullUrl ? $fullUrl : '';
            
            $data = [
                'user_id' => $user->id,
                'action' => 'login',
                'description' => "تسجيل دخول المستخدم: {$userName} ({$userRole})",
                'model_type' => get_class($user),
                'model_id' => $user->id,
                'old_values' => null,
                'new_values' => [
                    'login_time' => now()->toDateTimeString(),
                    'user_role' => $userRole,
                    'user_name' => $userName,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => $userAgent,
                'url' => $fullUrl,
                'method' => 'POST',
                'response_code' => 200,
                'duration' => null,
            ];
            
            // إضافة session_id إذا كان متاحاً
            if (session()->isStarted()) {
                try {
                    $data['session_id'] = session()->getId();
                } catch (\Exception $e) {
                    // تجاهل
                }
            }
            
            // استخدام DB مباشرة لتسريع العملية
            $insertData = array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            \DB::table('activity_logs')->insert($insertData);
        } catch (\Exception $e) {
            // تجاهل الأخطاء تماماً لتجنب منع تسجيل الدخول
        }
    }
}