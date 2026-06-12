<?php

namespace App\Models\Concerns;

use App\Support\BranchContext;
use Illuminate\Database\Eloquent\Builder;

/**
 * يقيّد الاستعلام على الفرع الحالي عندما يحدّد الـ middleware الفرع من المضيف.
 */
trait VisibleOnCurrentHostScope
{
    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeVisibleOnCurrentHost(Builder $query): Builder
    {
        $branch = app(BranchContext::class)->branch;
        if (! $branch) {
            return $query;
        }

        return $query->where($query->getModel()->getTable().'.branch_id', $branch->id);
    }
}
