<?php

namespace App\Support;

/**
 * أسماء الكلاسات القديمة بعد نقلها إلى app/Domains (انظر docs/MODULARIZATION_PLAN.md).
 *
 * عند نقل كلاس: نضيف سطراً هنا `'App\Models\Old' => \App\Domains\X\Models\Old::class`
 * فيستمر عمل كل مرجع قديم (use، type hints، Blade، الـ jobs المسلسلة في الـ queue،
 * وقيم model_type المحفوظة في activity_logs) حتى نحدّث المراجع ثم نحذف السطر.
 *
 * الـ alias يُنشأ عند أول استخدام للاسم القديم فقط (lazy) حتى لا نحمّل كل الكلاسات مع كل طلب.
 */
final class LegacyClassAliases
{
    /** @var array<string, class-string> الاسم القديم => الكلاس الجديد */
    public const MAP = [
        // Investment (2026-10-22)
        'App\Models\InvestmentInquiry' => \App\Domains\Investment\Models\InvestmentInquiry::class,
        'App\Models\InvestmentPlan' => \App\Domains\Investment\Models\InvestmentPlan::class,
        'App\Models\InvestmentPolicy' => \App\Domains\Investment\Models\InvestmentPolicy::class,
        'App\Services\Investment\InvestmentPlanService' => \App\Domains\Investment\Services\InvestmentPlanService::class,
        'App\Services\Investment\InvestmentStatsService' => \App\Domains\Investment\Services\InvestmentStatsService::class,
    ];

    private static bool $registered = false;

    public static function register(): void
    {
        if (self::$registered) {
            return;
        }
        self::$registered = true;

        spl_autoload_register(static function (string $class): void {
            if (isset(self::MAP[$class])) {
                class_alias(self::MAP[$class], $class);
            }
        });
    }

    /**
     * يحوّل اسم كلاس محفوظ في الداتابيز (قد يكون قديماً) إلى الكلاس الحالي.
     */
    public static function resolve(string $class): string
    {
        return self::MAP[ltrim($class, '\\')] ?? $class;
    }
}
