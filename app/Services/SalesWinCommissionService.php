<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesWinCommissionService
{
    /**
     * @return array{success: bool, transaction?: Transaction, commission?: float, error?: string}
     */
    public function approveAndPayCommission(SalesLead $lead, ?float $commissionAmount = null, ?string $notes = null): array
    {
        $lead->loadMissing(['assignee']);

        if ($lead->stage !== 'won') {
            return ['success' => false, 'error' => 'لا يمكن اعتماد الكوميشن إلا عند مرحلة «مكتمل / فوز».'];
        }
        if ($lead->won_confirmed_at) {
            return ['success' => false, 'error' => 'تم اعتماد هذا الـ lead مسبقاً.'];
        }
        if (! $lead->assignee || ! $lead->assignee->isSalesEmployee()) {
            return ['success' => false, 'error' => 'الموظف المسند ليس موظف مبيعات صالح.'];
        }

        $rep = $lead->assignee;
        $baseAmount = (float) ($lead->expected_value ?? 0);
        $commission = $commissionAmount !== null
            ? round($commissionAmount, 2)
            : $rep->calculateSalesCommissionAmount($baseAmount);

        DB::beginTransaction();
        try {
            $txnNumber = 'SALE-COM-' . str_pad((string) (Transaction::count() + 1), 6, '0', STR_PAD_LEFT);

            $txn = Transaction::create([
                'transaction_number' => $txnNumber,
                'user_id' => $rep->id,
                'payment_id' => null,
                'invoice_id' => null,
                'type' => 'credit',
                'category' => 'commission',
                'amount' => $commission,
                'currency' => 'EGP',
                'description' => 'عمولة مبيعات — اعتماد Lead فائز: ' . ($lead->name ?? ''),
                'status' => 'completed',
                'metadata' => [
                    'sales_lead_id' => $lead->id,
                    'assigned_to' => $rep->id,
                    'expected_value' => $baseAmount,
                    'commission_mode' => $rep->sales_commission_mode ?? 'none',
                    'commission_value' => (float) ($rep->sales_commission_value ?? 0),
                ],
                'created_by' => Auth::id(),
            ]);

            $lead->forceFill([
                'won_confirmed_at' => now(),
                'won_confirmed_by' => Auth::id(),
                'commission_amount' => $commission,
                'commission_transaction_id' => $txn->id,
                'commission_notes' => $notes,
            ])->save();

            SalesAuditService::log(
                'sales_lead_won_confirmed',
                $lead->fresh(),
                null,
                ['commission_amount' => $commission, 'transaction_id' => $txn->id],
                'اعتماد Lead فائز وصرف عمولة لموظف: ' . ($rep->name ?? '') . ' — Lead: ' . ($lead->name ?? '')
            );

            DB::commit();

            app(SalesNotificationService::class)->notifyCommissionPaid($rep, $lead->fresh(), $txn);

            return [
                'success' => true,
                'transaction' => $txn,
                'commission' => $commission,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return ['success' => false, 'error' => 'حدث خطأ أثناء الاعتماد.'];
        }
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function rejectWin(SalesLead $lead, string $reason, ?int $adminId = null): array
    {
        if ($lead->stage !== 'won' || $lead->won_confirmed_at) {
            return ['success' => false, 'error' => 'لا يمكن رفض هذه الصفقة.'];
        }

        $lead->loadMissing(['assignee']);
        $adminId ??= (int) Auth::id();
        $reason = trim($reason);
        if ($reason === '') {
            return ['success' => false, 'error' => 'سبب الرفض مطلوب.'];
        }

        $previousStage = 'won';
        $lead->forceFill([
            'stage' => 'proposal',
            'closed_at' => null,
            'won_confirmed_at' => null,
            'won_confirmed_by' => null,
            'commission_amount' => null,
            'commission_transaction_id' => null,
            'commission_notes' => null,
        ])->save();

        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $adminId,
            'type' => 'stage_change',
            'title' => 'رفض اعتماد الفوز',
            'body' => 'أُعيدت الصفقة إلى «عرض سعر» — السبب: ' . $reason,
            'meta' => ['from' => $previousStage, 'to' => 'proposal', 'rejected_by_admin' => true],
        ]);

        SalesAuditService::log(
            'sales_lead_won_rejected',
            $lead->fresh(),
            ['stage' => $previousStage],
            ['stage' => 'proposal', 'reason' => $reason],
            'رفض اعتماد فوز Lead: ' . ($lead->name ?? '')
        );

        if ($lead->assignee) {
            app(SalesNotificationService::class)->notifyWinRejected($lead->assignee, $lead->fresh(), $reason);
        }

        return ['success' => true];
    }

    public static function defaultCommissionForLead(SalesLead $lead): float
    {
        $lead->loadMissing(['assignee']);
        if (! $lead->assignee) {
            return 0.0;
        }

        return $lead->assignee->calculateSalesCommissionAmount((float) ($lead->expected_value ?? 0));
    }
}
