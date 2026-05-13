<?php

namespace App\Models\Concerns;

/**
 * استعلامات مساعدة للفرع (تقارير وفلاتر HQ لاحقاً).
 */
trait QueriesByBranch
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where($query->getModel()->getTable().'.branch_id', $branchId);
    }
}
