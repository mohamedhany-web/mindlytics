<?php

namespace App\Models\Concerns;

use App\Models\Branch;

/**
 * استعلامات مساعدة للفرع: فلترة بفرع محدد، أو بيانات الأكاديمية الأساسية فقط، أو فروع الامتداد فقط.
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

    /**
     * سجلات مرتبطة بفرع الأكاديمية الأساسية (slug عادةً `main`).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeForCentralAcademy($query)
    {
        $id = Branch::centralAcademyBranchId();
        if ($id === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where($query->getModel()->getTable().'.branch_id', $id);
    }

    /**
     * سجلات الفروع غير المركزية (امتدادات)، مع استبعاد فرع الأكاديمية الأساسية.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeForExtensionBranchesOnly($query)
    {
        $id = Branch::centralAcademyBranchId();
        if ($id === null) {
            return $query;
        }

        return $query->where($query->getModel()->getTable().'.branch_id', '!=', $id);
    }
}
