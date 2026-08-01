<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\SalesLeadCategory;
use Illuminate\Support\Collection;

class SalesCategoryStatsService
{
    /**
     * @return Collection<int, array{category: SalesLeadCategory, total: int, open: int, won_month: int, revenue_month: float, created_month: int, conversion: float|null, overdue: int}>
     */
    public function monthOverview(): Collection
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return SalesLeadCategory::query()->active()->ordered()->get()->map(function (SalesLeadCategory $category) use ($start, $end) {
            $base = SalesLead::query()->where('category_id', $category->id);
            $total = (int) (clone $base)->count();
            $open = (int) (clone $base)->openPipeline()->count();
            $createdMonth = (int) (clone $base)->whereBetween('created_at', [$start, $end])->count();
            $wonMonth = (int) (clone $base)->where('stage', SalesLead::WON_STAGE)->whereBetween('closed_at', [$start, $end])->count();
            $revenueMonth = (float) (clone $base)->where('stage', SalesLead::WON_STAGE)->whereBetween('closed_at', [$start, $end])->sum('expected_value');
            $overdue = (int) (clone $base)->openPipeline()
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now())
                ->count();

            $conversion = $createdMonth > 0 ? round($wonMonth / $createdMonth * 100, 1) : null;

            return [
                'category' => $category,
                'total' => $total,
                'open' => $open,
                'won_month' => $wonMonth,
                'revenue_month' => $revenueMonth,
                'created_month' => $createdMonth,
                'conversion' => $conversion,
                'overdue' => $overdue,
            ];
        })->filter(fn (array $row) => $row['total'] > 0 || $row['created_month'] > 0);
    }
}
