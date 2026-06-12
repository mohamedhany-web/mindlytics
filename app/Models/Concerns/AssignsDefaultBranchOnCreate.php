<?php

namespace App\Models\Concerns;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * عند إنشاء سجل جديد: تعيين branch_id من المستخدم المرتبط أو من الفرع الافتراضي.
 */
trait AssignsDefaultBranchOnCreate
{
    public static function bootAssignsDefaultBranchOnCreate(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getAttribute('branch_id') !== null && $model->getAttribute('branch_id') !== '') {
                return;
            }
            $userId = $model->getAttribute('user_id');
            if ($userId) {
                $bid = User::query()->whereKey($userId)->value('branch_id');
                if ($bid !== null) {
                    $model->setAttribute('branch_id', $bid);

                    return;
                }
            }
            $default = Branch::defaultAssignableId();
            if ($default !== null) {
                $model->setAttribute('branch_id', $default);
            }
        });
    }
}
