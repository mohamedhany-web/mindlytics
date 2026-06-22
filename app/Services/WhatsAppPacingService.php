<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class WhatsAppPacingService
{
    private static int $batchCounter = 0;

    public static function resetBatch(): void
    {
        self::$batchCounter = 0;
    }

    public function enabled(): bool
    {
        return (bool) config('whatsapp.pacing.enabled', true);
    }

    /**
     * @return string|null رسالة خطأ أو null إذا مسموح
     */
    public function assertCanSend(): ?string
    {
        if (! $this->enabled()) {
            return null;
        }

        if ($this->isOutsideBusinessHours()) {
            $start = config('whatsapp.pacing.business_start', 9);
            $end = config('whatsapp.pacing.business_end', 21);

            return "الإرسال متاح فقط بين {$start}:00 و {$end}:00 لتقليل خطر الحظر.";
        }

        $hourKey = 'wa_sent_hour_' . now()->format('Y-m-d-H');
        $dayKey = 'wa_sent_day_' . now()->format('Y-m-d');

        $hourCount = (int) Cache::get($hourKey, 0);
        $dayCount = (int) Cache::get($dayKey, 0);

        $maxHour = (int) config('whatsapp.pacing.max_per_hour', 70);
        $maxDay = (int) config('whatsapp.pacing.max_per_day', 320);

        if ($hourCount >= $maxHour) {
            return "تم الوصول للحد الأقصى {$maxHour} رسالة/ساعة. انتظر الساعة التالية.";
        }

        if ($dayCount >= $maxDay) {
            return "تم الوصول للحد الأقصى {$maxDay} رسالة/يوم. جرّب غداً أو استخدم Meta API.";
        }

        return null;
    }

    public function waitBeforeSend(?int $batchId = null): void
    {
        if (! $this->enabled()) {
            return;
        }

        $counter = $this->incrementBatchCounter($batchId);

        $pauseEvery = max(0, (int) config('whatsapp.pacing.pause_every', 20));
        if ($pauseEvery > 0 && $counter > 1 && ($counter - 1) % $pauseEvery === 0) {
            $pauseMin = (int) config('whatsapp.pacing.pause_min_seconds', 50);
            $pauseMax = (int) config('whatsapp.pacing.pause_max_seconds', 110);
            $pause = random_int(min($pauseMin, $pauseMax), max($pauseMin, $pauseMax));
            sleep($pause);
        }

        $min = (int) config('whatsapp.pacing.min_delay_seconds', 5);
        $max = (int) config('whatsapp.pacing.max_delay_seconds', 14);
        $delay = random_int(min($min, $max), max($min, $max));
        sleep($delay);
    }

    public function recordSuccessfulSend(): void
    {
        if (! $this->enabled()) {
            return;
        }

        $hourKey = 'wa_sent_hour_' . now()->format('Y-m-d-H');
        $dayKey = 'wa_sent_day_' . now()->format('Y-m-d');

        if (! Cache::has($hourKey)) {
            Cache::put($hourKey, 0, now()->endOfHour());
        }
        if (! Cache::has($dayKey)) {
            Cache::put($dayKey, 0, now()->endOfDay());
        }

        Cache::increment($hourKey);
        Cache::increment($dayKey);
    }

    public function simulateTyping(): bool
    {
        return (bool) config('whatsapp.pacing.simulate_typing', true);
    }

    /**
     * @return array{hour: int, day: int, max_hour: int, max_day: int}
     */
    public function usageStats(): array
    {
        $hourKey = 'wa_sent_hour_' . now()->format('Y-m-d-H');
        $dayKey = 'wa_sent_day_' . now()->format('Y-m-d');

        return [
            'hour' => (int) Cache::get($hourKey, 0),
            'day' => (int) Cache::get($dayKey, 0),
            'max_hour' => (int) config('whatsapp.pacing.max_per_hour', 70),
            'max_day' => (int) config('whatsapp.pacing.max_per_day', 320),
        ];
    }

    private function isOutsideBusinessHours(): bool
    {
        if (! config('whatsapp.pacing.business_hours_only', true)) {
            return false;
        }

        $hour = (int) now()->format('G');
        $start = (int) config('whatsapp.pacing.business_start', 9);
        $end = (int) config('whatsapp.pacing.business_end', 21);

        return $hour < $start || $hour >= $end;
    }

    private function incrementBatchCounter(?int $batchId): int
    {
        if ($batchId === null) {
            self::$batchCounter++;

            return self::$batchCounter;
        }

        $key = 'wa_batch_counter_' . $batchId;

        if (! Cache::has($key)) {
            Cache::put($key, 0, now()->addHours(24));
        }

        return (int) Cache::increment($key);
    }
}
