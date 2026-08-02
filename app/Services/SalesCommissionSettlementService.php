<?php

namespace App\Services;

use App\Models\SalesCommissionSettlement;
use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class SalesCommissionSettlementService
{
    public function ready(): bool
    {
        return Schema::hasTable('sales_commission_settlements')
            && Schema::hasColumn('sales_leads', 'commission_settled_at');
    }

    /**
     * @return Collection<int, SalesLead>
     */
    public function unsettledLeadsFor(User $rep): Collection
    {
        return SalesLead::query()
            ->where('assigned_to', $rep->id)
            ->where('stage', SalesLead::WON_STAGE)
            ->whereNotNull('won_confirmed_at')
            ->whereNull('commission_settled_at')
            ->where('commission_amount', '>', 0)
            ->orderBy('won_confirmed_at')
            ->orderBy('id')
            ->get();
    }

    /**
     * مخالصة: تعليم كوميشن الـ leads كـ «تم الدفع» للموظف.
     *
     * @param  list<int>|null  $leadIds  null = كل المستحق
     * @return array{success: bool, settlement?: SalesCommissionSettlement, error?: string, count?: int, amount?: float}
     */
    public function settle(User $rep, ?array $leadIds = null, ?string $notes = null, ?int $settledBy = null): array
    {
        if (! $this->ready()) {
            return ['success' => false, 'error' => 'شغّل migrate أولاً لجداول المخالصة'];
        }

        if (! $rep->isSalesEmployee()) {
            return ['success' => false, 'error' => 'المستخدم ليس موظف مبيعات'];
        }

        $query = SalesLead::query()
            ->where('assigned_to', $rep->id)
            ->where('stage', SalesLead::WON_STAGE)
            ->whereNotNull('won_confirmed_at')
            ->whereNull('commission_settled_at')
            ->where('commission_amount', '>', 0);

        if ($leadIds !== null) {
            $ids = array_values(array_unique(array_map('intval', $leadIds)));
            if ($ids === []) {
                return ['success' => false, 'error' => 'اختر صفقة واحدة على الأقل'];
            }
            $query->whereIn('id', $ids);
        }

        $leads = $query->orderBy('id')->get();
        if ($leads->isEmpty()) {
            return ['success' => false, 'error' => 'لا يوجد كوميشن مستحق للمخالصة'];
        }

        $settledBy = $settledBy ?: (int) Auth::id();
        $amount = round((float) $leads->sum('commission_amount'), 2);

        try {
            $settlement = DB::transaction(function () use ($rep, $leads, $notes, $settledBy, $amount) {
                $settlement = SalesCommissionSettlement::query()->create([
                    'user_id' => $rep->id,
                    'settled_by' => $settledBy ?: null,
                    'leads_count' => $leads->count(),
                    'amount_total' => $amount,
                    'notes' => $notes,
                    'settled_at' => now(),
                    'meta' => [
                        'lead_ids' => $leads->pluck('id')->all(),
                    ],
                ]);

                SalesLead::query()
                    ->whereIn('id', $leads->pluck('id')->all())
                    ->update([
                        'commission_settled_at' => now(),
                        'commission_settled_by' => $settledBy ?: null,
                        'commission_settlement_id' => $settlement->id,
                    ]);

                SalesAuditService::log(
                    'sales_commission_settled',
                    $rep,
                    null,
                    [
                        'settlement_id' => $settlement->id,
                        'leads_count' => $leads->count(),
                        'amount_total' => $amount,
                        'lead_ids' => $leads->pluck('id')->all(),
                    ],
                    'مخالصة كوميشن لـ '.($rep->name ?? '').' — '.number_format($amount, 2).' ج.م ('.$leads->count().' صفقة)'
                );

                return $settlement;
            });
        } catch (\Throwable $e) {
            report($e);

            return ['success' => false, 'error' => 'فشل حفظ المخالصة'];
        }

        return [
            'success' => true,
            'settlement' => $settlement,
            'count' => $leads->count(),
            'amount' => $amount,
        ];
    }

    /**
     * إلغاء تسوية صفقة واحدة (ترجع لمستحق) — للإدارة فقط.
     */
    public function unsettleLead(SalesLead $lead): void
    {
        if (! $this->ready() || ! $lead->commission_settled_at) {
            throw ValidationException::withMessages(['lead' => 'الصفقة غير مسوّاة']);
        }

        $lead->forceFill([
            'commission_settled_at' => null,
            'commission_settled_by' => null,
            'commission_settlement_id' => null,
        ])->save();
    }
}
