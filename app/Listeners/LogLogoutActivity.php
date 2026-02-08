<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogLogoutActivity
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
    public function handle(Logout $event): void
    {
        try {
            $user = $event->user;
            
            if ($user) {
                $userRole = isset($user->role) ? $user->role : 'unknown';
                $userName = isset($user->name) ? $user->name : 'Unknown';
                $userAgent = request()->userAgent();
                $userAgent = $userAgent ? $userAgent : '';
                $fullUrl = request()->fullUrl();
                $fullUrl = $fullUrl ? $fullUrl : '';
                
                $newValues = [
                    'logout_time' => now()->toDateTimeString(),
                    'user_role' => $userRole,
                    'user_name' => $userName,
                ];
                
                $insertData = [
                    'user_id' => $user->id,
                    'action' => 'logout',
                    'description' => "تسجيل خروج المستخدم: {$userName} ({$userRole})",
                    'model_type' => get_class($user),
                    'model_id' => $user->id,
                    'old_values' => null,
                    'new_values' => \json_encode($newValues),
                    'ip_address' => request()->ip(),
                    'user_agent' => $userAgent,
                    'url' => $fullUrl,
                    'method' => 'POST',
                    'response_code' => 200,
                    'duration' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (session()->isStarted()) {
                    try {
                        $insertData['session_id'] = session()->getId();
                    } catch (\Exception $e) {
                        // تجاهل
                    }
                }
                
                \DB::table('activity_logs')->insert($insertData);
            }
        } catch (\Exception $e) {
            // تجاهل الأخطاء تماماً لتجنب منع تسجيل الخروج
            \Log::debug('Failed to log logout activity: ' . $e->getMessage());
        }
    }
}