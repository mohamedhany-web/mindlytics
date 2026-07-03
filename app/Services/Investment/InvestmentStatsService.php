<?php

namespace App\Services\Investment;

use App\Models\InvestmentInquiry;
use App\Models\InvestmentPlan;
use Illuminate\Support\Facades\Schema;

class InvestmentStatsService
{
    /**
     * @return array<string, int|float>
     */
    public function overview(): array
    {
        if (! Schema::hasTable('investment_plans')) {
            return $this->emptyOverview();
        }

        return [
            'plans_total' => InvestmentPlan::count(),
            'plans_active' => InvestmentPlan::where('is_active', true)->count(),
            'inquiries_total' => InvestmentInquiry::count(),
            'pending' => InvestmentInquiry::where('status', InvestmentInquiry::STATUS_PENDING)->count(),
            'under_review' => InvestmentInquiry::where('status', InvestmentInquiry::STATUS_UNDER_REVIEW)->count(),
            'approved' => InvestmentInquiry::where('status', InvestmentInquiry::STATUS_APPROVED)->count(),
            'proposed_total' => (float) InvestmentInquiry::whereNotIn('status', [
                InvestmentInquiry::STATUS_REJECTED,
                InvestmentInquiry::STATUS_WITHDRAWN,
            ])->sum('proposed_amount'),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function emptyOverview(): array
    {
        return [
            'plans_total' => 0,
            'plans_active' => 0,
            'inquiries_total' => 0,
            'pending' => 0,
            'under_review' => 0,
            'approved' => 0,
            'proposed_total' => 0,
        ];
    }
}
