<?php

namespace App\Support;

/**
 * تطبيع رقم الهاتف المُدخل إلى الرقم الوطني حسب إعدادات الدولة (config/phone_countries.php).
 * يقبل الصيغ الشائعة: 07xxxxxxxx أو 7xxxxxxxx أو +962 7xxxxxxxx أو 00962 7xxxxxxxx أو 962 7xxxxxxxx.
 */
final class PhoneCountry
{
    /**
     * يُرجع الرقم الوطني (بدون كود الدولة وبدون صفر البداية).
     * إن لم تطابق أي صيغة تحقق الدولة يُرجع الأرقام بعد حذف أصفار البداية ليفشل التحقق كالمعتاد.
     *
     * @param  array<string, mixed>  $country
     */
    public static function nationalNumber(array $country, ?string $phone): string
    {
        $local = ltrim(preg_replace('/\D/', '', (string) $phone), '0');
        $regex = $country['validation']['regex'] ?? null;
        $dialDigits = preg_replace('/\D/', '', (string) ($country['dial_code'] ?? ''));

        if ($regex === null || $dialDigits === '' || preg_match($regex, $local)) {
            return $local;
        }

        // الصيغة الدولية: +962 / 00962 / 962 (أصفار البداية حُذفت مسبقاً)، مع احتمال صفر محلي بعد الكود
        if (str_starts_with($local, $dialDigits)) {
            $withoutDial = ltrim(substr($local, strlen($dialDigits)), '0');
            if (preg_match($regex, $withoutDial)) {
                return $withoutDial;
            }
        }

        return $local;
    }
}
