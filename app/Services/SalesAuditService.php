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

        ActivityLog::logActivity($action, $model, $oldValues, $newValues, $description);
    }
}
