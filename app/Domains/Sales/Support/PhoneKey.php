<?php

namespace App\Domains\Sales\Support;

/**
 * مفتاح مقارنة أرقام الهاتف: الأرقام محفوظة بصيغ مختلفة (+20 10…، 010…، 0020 10…، +962 7…).
 * آخر 9 أرقام تكفي لتمييز رقم الموبايل في مصر والأردن بعد حذف رمز الدولة والصفر الأول.
 */
final class PhoneKey
{
    public const LENGTH = 9;

    public static function of(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';
        if (strlen($digits) < 8) {
            return null;
        }

        return substr($digits, -self::LENGTH);
    }

    public static function same(?string $a, ?string $b): bool
    {
        $ka = self::of($a);

        return $ka !== null && $ka === self::of($b);
    }

    /**
     * جزء يُستخدم في LIKE لتضييق البحث قبل المقارنة الدقيقة (آخر 7 أرقام لا تتأثر بمسافات أو شرطات قبلها غالباً).
     */
    public static function likeTail(?string $phone): ?string
    {
        $key = self::of($phone);

        return $key === null ? null : '%'.substr($key, -7);
    }
}
