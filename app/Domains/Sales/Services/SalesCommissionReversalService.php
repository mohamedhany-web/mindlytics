<?php

namespace App\Domains\Sales\Services;

use App\Domains\Accounting\Events\PaymentRefunded;
use App\Domains\Accounting\Models\Order;
use App\Domains\Accounting\Models\Transaction;
use App\Domains\Accounting\Support\DocumentNumber;
use App\Domains\Hr\Models\EmployeeAgreement;
use App\Domains\Hr\Models\EmployeeSalaryDeduction;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * استرجاع عمولة المبيعات عند استرداد قيمة الطلب (كلياً أو جزئياً) — docs/SALES_INTEGRITY.md.
 *
 * commission_amount يبقى كما اعتُمد؛ الجزء المسترجع يُسجّل في commission_reversed_amount بنسبة المسترد من الطلب:
 * - لم تُصرف المخالصة بعد: المخالصة القادمة تدفع الصافي فقط.
 * - صُرفت بالفعل: يُنشأ خصم «معلّق» على الموظف تعتمده الموارد البشرية (لا خصم تلقائي من المرتب).
 * في الحالتين تُسجّل معاملة debit بنفس قيمة الاسترجاع في المحاسبة.
 */
class SalesCommissionReversalService
{
    public function __construct(private SalesLeadOrderLinker $linker) {}

    public function handle(PaymentRefunded $event): void
    {
        if (! SalesLeadOrderLinker::ready()) {
            return;
        }
        try {
            $orderIds = Order::query()->where('payment_id', $event->paymentId)->pluck('id');
            if ($orderIds->isEmpty()) {
                return;
            }
            SalesLead::query()->whereIn('order_id', $orderIds)->whereNotNull('won_confirmed_at')->pluck('id')
                ->each(fn ($id) => $this->reverseFor((int) $id, $event));
        } catch (\Throwable $e) {
            Log::error('Sales: commission reversal failed', ['payment_id' => $event->paymentId, 'message' => $e->getMessage()]);
        }
    }

    public function reverseFor(int $leadId, ?PaymentRefunded $event = null): ?Transaction
    {
        return DB::transaction(function () use ($leadId, $event) {
            $lead = SalesLead::query()->whereKey($leadId)->lockForUpdate()->firstOrFail();
            $order = $lead->order_id ? Order::query()->with('payment')->find($lead->order_id) : null;
            $commission = (float) ($lead->commission_amount ?? 0);
            if (! $order || $commission <= 0 || (float) $order->amount <= 0) {
                return null;
            }

            $refundedShare = min(1.0, max(0.0, 1 - $this->linker->netPaid($order) / (float) $order->amount));
            $target = round($commission * $refundedShare, 2);
            $already = (float) ($lead->commission_reversed_amount ?? 0);
            $delta = round($target - $already, 2);
            if ($delta < 0.01) {
                return null;
            }

            $rep = $lead->assignee;
            $txn = Transaction::create([
                'transaction_number' => DocumentNumber::next(Transaction::class, 'transaction_number', 'SALE-REV-', 6),
                'user_id' => $lead->assigned_to,
                'type' => 'debit',
                'category' => 'commission',
                'amount' => $delta,
                'currency' => 'EGP',
                'description' => 'استرجاع عمولة مبيعات بعد استرداد الطلب #'.$order->id.' — Lead: '.($lead->name ?? ''),
                'status' => 'completed',
                'metadata' => [
                    'kind' => 'commission_reversal',
                    'sales_lead_id' => $lead->id,
                    'order_id' => $order->id,
                    'payment_id' => $order->payment_id,
                    'refund_transaction_id' => $event?->refundTransactionId,
                    'reversal_of_transaction_id' => $lead->commission_transaction_id,
                    'refunded_share' => round($refundedShare, 4),
                ],
            ]);

            $deduction = null;
            if ($lead->commission_settled_at && $rep) {
                // العمولة صُرفت للموظف بالفعل: خصم مقترح ينتظر اعتماد الموارد البشرية
                $deduction = EmployeeSalaryDeduction::createWithAutoDeductionNumber([
                    'employee_id' => $rep->id,
                    'agreement_id' => EmployeeAgreement::query()->where('employee_id', $rep->id)->where('status', 'active')->value('id'),
                    'title' => 'استرجاع عمولة مبيعات',
                    'description' => 'استرداد الطلب #'.$order->id.' بعد صرف عمولة الـ Lead «'.($lead->name ?? '').'» — الجزء المسترجع '.number_format($delta, 2).' ج.م',
                    'amount' => $delta,
                    'type' => 'other',
                    'deduction_date' => now()->toDateString(),
                    'status' => 'pending',
                    'notes' => 'sales_commission_reversal lead:'.$lead->id.' order:'.$order->id,
                    'created_by' => null,
                ]);
            }

            $lead->forceFill([
                'commission_reversed_amount' => round($already + $delta, 2),
                'commission_reversed_at' => now(),
            ])->save();

            SalesActivity::create([
                'sales_lead_id' => $lead->id,
                'user_id' => $lead->assigned_to ?? $order->user_id,
                'type' => 'note',
                'title' => 'استرجاع عمولة بعد استرداد',
                'body' => 'تم استرداد '.number_format($refundedShare * 100, 0).'٪ من الطلب #'.$order->id
                    .' فاسترجع '.number_format($delta, 2).' ج.م من العمولة'
                    .($deduction ? ' (خصم معلّق رقم '.$deduction->deduction_number.' لأن العمولة صُرفت)' : ' (تُخصم من المخالصة القادمة)'),
                'meta' => ['order_id' => $order->id, 'reversal_transaction_id' => $txn->id, 'deduction_id' => $deduction?->id],
            ]);

            SalesAuditService::log('sales_commission_reversed', $lead, ['commission_reversed_amount' => $already],
                ['commission_reversed_amount' => $already + $delta, 'transaction_id' => $txn->id, 'deduction_id' => $deduction?->id],
                'استرجاع عمولة '.number_format($delta, 2).' ج.م — Lead #'.$lead->id);

            return $txn;
        });
    }
}
