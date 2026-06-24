<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class SalesAuditService
{
    /**
     * تسجيل كل إجراء مبيعات في سجل الأنشطة العام (مراقبة الإدارة).
     */
    public static function log(string $action, ?Model $model = null, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): void
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        try {
            ActivityLog::logActivity(
                $action,
                $model,
                self::normalizeAuditValues($oldValues),
                self::normalizeAuditValues($newValues),
                $description
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * @param  array<string, mixed>|null  $values
     * @return array<string, mixed>|null
     */
    private static function normalizeAuditValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $normalized = [];
        foreach ($values as $key => $value) {
            if ($value instanceof \DateTimeInterface) {
                $normalized[$key] = $value->format('Y-m-d H:i:s');
            } elseif (is_object($value) && ! is_array($value)) {
                $normalized[$key] = method_exists($value, '__toString') ? (string) $value : json_decode(json_encode($value), true);
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }
}
