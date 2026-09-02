<?php

namespace App\Support;

class AcademyWhatsApp
{
    public static function academyDigits(): string
    {
        $wa = (string) (PlatformSettings::contactPage()['whatsapp'] ?? '');
        $digits = preg_replace('/\D+/', '', $wa) ?: '201044610507';

        return $digits;
    }

    /**
     * Build wa.me URL with a structured booking message for academy staff.
     *
     * @param  array{
     *   type?: string,
     *   title?: string,
     *   code?: string,
     *   url?: string,
     *   price?: string|float|null,
     *   channel?: string|null
     * }  $item
     */
    public static function bookingUrl(array $item, ?\App\Models\User $user = null): string
    {
        $user = $user ?: auth()->user();
        $type = (string) ($item['type'] ?? 'course');
        $title = (string) ($item['title'] ?? '');
        $code = (string) ($item['code'] ?? '');
        $url = (string) ($item['url'] ?? '');
        $price = $item['price'] ?? null;
        $channel = (string) ($item['channel'] ?? '');

        $typeLabel = match ($type) {
            'path', 'learning_path' => 'مسار تعليمي كامل',
            'recorded', 'course' => 'كورس مسجّل',
            'live_offline', 'offline' => 'جروب لايف أوفلاين',
            'live_online', 'online' => 'جروب لايف أونلاين',
            default => 'طلب حجز',
        };

        $lines = [
            'مرحباً أكاديمية Mindlytics 👋',
            'أرغب في الحجز عبر المنصة:',
            'النوع: ' . $typeLabel,
            'الاسم: ' . $title,
        ];

        if ($code !== '') {
            $lines[] = 'الكود/المعرّف: ' . $code;
        }
        if ($channel !== '') {
            $lines[] = 'القناة: ' . $channel;
        }
        if ($price !== null && $price !== '') {
            $lines[] = 'السعر: ' . $price . ' ج.م';
        }
        if ($url !== '') {
            $lines[] = 'الرابط: ' . $url;
        }
        if ($user) {
            $lines[] = '---';
            $lines[] = 'بيانات الطالب:';
            $lines[] = 'الاسم: ' . ($user->name ?? '');
            if (! empty($user->phone)) {
                $lines[] = 'الهاتف: ' . $user->phone;
            }
            if (! empty($user->email)) {
                $lines[] = 'البريد: ' . $user->email;
            }
            $lines[] = 'معرّف الحساب: #' . $user->id;
        }
        $lines[] = 'من فضلك أكّدوا التوفر وأرسلوا طريقة الدفع أو فعّلوا الاشتراك بعد التحويل.';

        $text = implode("\n", $lines);

        return 'https://wa.me/' . self::academyDigits() . '?text=' . rawurlencode($text);
    }
}
