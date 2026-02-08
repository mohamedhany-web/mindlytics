<?php

namespace App\Observers;

use App\Models\User;
use App\Services\StatisticsCacheService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            $this->clearCache();
        } catch (\Exception $e) {
            // لا نوقف عملية إنشاء المستخدم إذا فشل مسح الكاش
            Log::warning('Failed to clear cache after user creation: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        try {
            // مسح الكاش فقط عند تغيير الحقول المهمة
            if ($user->isDirty(['role', 'is_active'])) {
                $this->clearCache();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear cache after user update: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        try {
            $this->clearCache();
        } catch (\Exception $e) {
            Log::warning('Failed to clear cache after user deletion: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
            ]);
        }
    }

    /**
     * مسح الكاش
     */
    private function clearCache(): void
    {
        try {
            $statsService = app(StatisticsCacheService::class);
            $statsService->clearStats('user_stats');
            $statsService->clearStats('dashboard_stats');
        } catch (\Exception $e) {
            Log::warning('Failed to clear statistics cache: ' . $e->getMessage());
            // لا نرمي exception لأن هذا لا يجب أن يوقف العملية
        }
    }
}
