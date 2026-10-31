<?php

namespace App\Domains\Accounting\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * ترقيم المستندات المالية (INV-00000001, PAY-…, TXN-…, EXP-…, WDR-2026-000001 …).
 *
 * كان الرقم = count() + 1، وهذا يتكرر بعد حذف أي صف (الأعمدة unique فيفشل الإدخال) أو عند عمليتين متزامنتين.
 * هنا: الرقم = max(عدد الصفوف، أكبر رقم موجود بنفس البادئة) + 1.
 * بدون حذف يساوي count() + 1 تماماً، فلا تتغير الأرقام الحالية على الإنتاج (docs/ACCOUNTING_AUDIT.md → C2).
 *
 * داخل transaction يُقفل آخر صف بنفس البادئة أولاً، فينتظر الطلب المتزامن حتى يكتمل الأول ثم يرى رقمه.
 */
final class DocumentNumber
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function next(string $modelClass, string $column, string $prefix, int $pad): string
    {
        /** @var Model $model */
        $model = new $modelClass;
        $base = fn () => $modelClass::query()->withoutGlobalScopes();

        if (DB::connection($model->getConnectionName())->transactionLevel() > 0) {
            $base()->where($column, 'like', $prefix.'%')
                ->orderByDesc($model->getKeyName())
                ->limit(1)
                ->lockForUpdate()
                ->value($column);
        }

        $next = max($base()->count(), self::highestSuffix($base(), $column, $prefix)) + 1;

        return $prefix.str_pad((string) $next, $pad, '0', STR_PAD_LEFT);
    }

    /**
     * أكبر رقم بعد البادئة. الأرقام مكمّلة بأصفار فالأطول ثم الأكبر نصياً هو الأكبر رقمياً؛
     * نقرأ أول 20 فقط ونتجاهل أي قيمة ليست أرقاماً بعد البادئة.
     */
    private static function highestSuffix($query, string $column, string $prefix): int
    {
        $values = $query->where($column, 'like', $prefix.'%')
            ->orderByRaw('LENGTH('.$query->getQuery()->getGrammar()->wrap($column).') DESC')
            ->orderByDesc($column)
            ->limit(20)
            ->pluck($column);

        $highest = 0;
        foreach ($values as $value) {
            $suffix = substr((string) $value, strlen($prefix));
            if ($suffix !== '' && ctype_digit($suffix)) {
                $highest = max($highest, (int) $suffix);
            }
        }

        return $highest;
    }
}
