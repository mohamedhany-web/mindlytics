<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStudentRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public ?string $referralCode = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = User::findOrFail($this->userId);

            // معالجة كود الإحالة إذا كان موجوداً
            if ($this->referralCode) {
                $referralService = app(ReferralService::class);
                $referralCode = strtoupper(trim($this->referralCode));
                $referrer = User::where('referral_code', $referralCode)->first();
                
                if ($referrer && $referrer->id !== $user->id) {
                    $referralService->processReferral($referrer, $user, $referralCode);
                }
            }

            // إنشاء كود إحالة للمستخدم الجديد
            $referralService = app(ReferralService::class);
            $referralService->generateReferralCode($user);

            // مسح الكاش للإحصائيات
            app(\App\Services\StatisticsCacheService::class)->clearStats('user_stats');

            Log::info("تم معالجة تسجيل الطالب بنجاح: {$user->id}");
        } catch (\Exception $e) {
            Log::error("خطأ في معالجة تسجيل الطالب: {$e->getMessage()}", [
                'user_id' => $this->userId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
