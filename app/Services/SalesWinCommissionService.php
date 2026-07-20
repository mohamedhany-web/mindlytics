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
     * @return array{success: bool, transaction?: Transaction, commission?: float, bonus?: float, error?: string}
     */
    public function approveAndPayCommission(SalesLead $lead, ?float $commissionAmount = null, ?string $notes = null): array
    {
        $lead->loadMissing(['assignee', 'advancedCourse', 'offlineCourse', 'legacyCourse']);

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
        $resolver = app(SalesCourseCommissionResolver::class);
        $quote = $resolver->quoteForLead($rep, $lead);
        $bonus = 0.0;
        $saleNumber = $quote['sale_number'] ?? null;

        if ($commissionAmount !== null) {
            $commission = round($commissionAmount, 2);
            $calcMode = 'manual';
        } else {
            $commission = (float) $quote['commission'];
            $bonus = (float) $quote['bonus'];
            $calcMode = (string) $quote['calc_mode'];
        }

        DB::beginTransaction();
        try {
            $txnNumber = 'SALE-COM-'.str_pad((string) (Transaction::count() + 1), 6, '0', STR_PAD_LEFT);

            $metadata = [
                'sales_lead_id' => $lead->id,
                'assigned_to' => $rep->id,
                'expected_value' => $baseAmount,
                'commission_mode' => $calcMode,
                'commission_value' => (float) ($rep->sales_commission_value ?? 0),
                'quote_source' => $quote['source'] ?? 'user',
                'agreement_id' => $quote['agreement']?->id,
                'course_type' => $quote['course_type'] ?? $lead->course_type,
                'course_id' => $quote['course_id'] ?? $lead->linkedCourseId(),
                'calc_mode' => $calcMode,
            ];

            if ($saleNumber !== null) {
                $metadata['tier_sale_number'] = $saleNumber;
                $metadata['tier_rate'] = $quote['rate'] ?? $commission;
                $metadata['tier_bonus'] = $bonus;
            }

            $descExtra = '';
            if ($saleNumber !== null) {
                $descExtra = ' (بيع رقم '.$saleNumber.' × '.number_format((float) ($quote['rate'] ?? $commission), 2).' ج.م)';
            }
            if ($lead->linkedCourseTitle()) {
                $descExtra .= ' — '.$lead->linkedCourseTypeLabel().': '.$lead->linkedCourseTitle();
            }

            $txn = Transaction::create([
                'transaction_number' => $txnNumber,
                'user_id' => $rep->id,
                'payment_id' => null,
                'invoice_id' => null,
                'type' => 'credit',
                'category' => 'commission',
                'amount' => $commission,
                'currency' => 'EGP',
                'description' => 'عمولة مبيعات — اعتماد Lead فائز: '.($lead->name ?? '').$descExtra,
                'status' => 'completed',
                'metadata' => $metadata,
                'created_by' => Auth::id(),
            ]);

            $bonusTxn = null;
            if ($bonus > 0.009) {
                $bonusTxnNumber = 'SALE-BON-'.str_pad((string) (Transaction::count() + 1), 6, '0', STR_PAD_LEFT);
                $bonusTxn = Transaction::create([
                    'transaction_number' => $bonusTxnNumber,
                    'user_id' => $rep->id,
                    'payment_id' => null,
                    'invoice_id' => null,
                    'type' => 'credit',
                    'category' => 'commission',
                    'amount' => $bonus,
                    'currency' => 'EGP',
                    'description' => 'بونص Tier — الوصول لـ '.($saleNumber ?? '').' مبيعات — Lead: '.($lead->name ?? ''),
                    'status' => 'completed',
                    'metadata' => [
                        'sales_lead_id' => $lead->id,
                        'assigned_to' => $rep->id,
                        'kind' => 'tier_milestone_bonus',
                        'tier_sale_number' => $saleNumber,
                        'tier_bonus' => $bonus,
                        'agreement_id' => $quote['agreement']?->id,
                        'calc_mode' => $calcMode,
                    ],
                    'created_by' => Auth::id(),
                ]);
            }

            $commissionNotes = $notes;
            $autoNote = 'وضع: '.$calcMode;
            if ($saleNumber !== null) {
                $autoNote .= ' | Tier #'.$saleNumber.' — عمولة '.number_format($commission, 2).' ج.م'
                    .($bonus > 0 ? ' + بونص '.number_format($bonus, 2).' ج.م' : '');
            } else {
                $autoNote .= ' — عمولة '.number_format($commission, 2).' ج.م';
            }
            if ($lead->linkedCourseTitle()) {
                $autoNote .= ' | كورس: '.$lead->linkedCourseTitle();
            }
            $commissionNotes = trim(($commissionNotes ? $commissionNotes.' | ' : '').$autoNote);

            $lead->forceFill([
                'won_confirmed_at' => now(),
                'won_confirmed_by' => Auth::id(),
                'commission_amount' => round($commission + $bonus, 2),
                'commission_transaction_id' => $txn->id,
                'commission_notes' => $commissionNotes,
            ])->save();

            SalesAuditService::log(
                'sales_lead_won_confirmed',
                $lead->fresh(),
                null,
                [
                    'commission_amount' => $commission,
                    'bonus_amount' => $bonus,
                    'transaction_id' => $txn->id,
                    'bonus_transaction_id' => $bonusTxn?->id,
                    'tier_sale_number' => $saleNumber,
                    'agreement_id' => $quote['agreement']?->id,
                    'calc_mode' => $calcMode,
                ],
                'اعتماد Lead فائز وصرف عمولة لموظف: '.($rep->name ?? '').' — Lead: '.($lead->name ?? '')
            );

            DB::commit();

            app(SalesNotificationService::class)->notifyCommissionPaid($rep, $lead->fresh(), $txn);

            return [
                'success' => true,
                'transaction' => $txn,
                'commission' => $commission,
                'bonus' => $bonus,
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
            'body' => 'أُعيدت الصفقة إلى «عرض سعر» — السبب: '.$reason,
            'meta' => ['from' => $previousStage, 'to' => 'proposal', 'rejected_by_admin' => true],
        ]);

        SalesAuditService::log(
            'sales_lead_won_rejected',
            $lead->fresh(),
            ['stage' => $previousStage],
            ['stage' => 'proposal', 'reason' => $reason],
            'رفض اعتماد فوز Lead: '.($lead->name ?? '')
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

        return (float) app(SalesCourseCommissionResolver::class)
            ->quoteForLead($lead->assignee, $lead)['total'];
    }
}
