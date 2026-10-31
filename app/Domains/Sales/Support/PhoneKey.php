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
     * رابط wa.me بالصيغة الدولية: 010… ← 2010… (مصر)، 07… ← 9627… (الأردن)، والدولي كما هو.
     */
    public static function whatsappUrl(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if ($digits === '' || strlen($digits) < 8) {
            return null;
        }
        if (str_starts_with($digits, '07') && strlen($digits) === 10) {
            $digits = '962'.substr($digits, 1);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '20'.substr($digits, 1);
        } elseif (str_starts_with($digits, '1') && strlen($digits) === 10) {
            $digits = '20'.$digits;
        }

        return 'https://wa.me/'.$digits;
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
