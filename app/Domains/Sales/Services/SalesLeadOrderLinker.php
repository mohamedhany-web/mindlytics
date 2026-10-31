<?php

namespace App\Domains\Sales\Services;

use App\Domains\Accounting\Models\Order;
use App\Domains\Accounting\Models\Transaction;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Support\PhoneKey;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * يربط الـ Lead بالطلب المدفوع الحقيقي (docs/SALES_INTEGRITY.md):
 * عند اعتماد أي طلب (يدوي، كاشير، فواتيرك، أوفلاين) يُبحث عن Lead لنفس الطالب (حساب / هاتف / بريد)
 * عمل عليه موظف مبيعات خلال نافذة الإسناد، فيُربط به ويصبح «تسجيل مكتمل» بقيمة الطلب الفعلية.
 * اعتماد العمولة يبقى قراراً بشرياً من الأدمن، لكنه يُبنى على المبلغ المدفوع لا على رقم يكتبه الموظف.
 */
class SalesLeadOrderLinker
{
    private static ?bool $ready = null;

    public static function ready(): bool
    {
        return self::$ready ??= Schema::hasTable('sales_leads') && Schema::hasColumn('sales_leads', 'order_id');
    }

    /** يُستدعى بعد حفظ طلب معتمد. لا يرمي أبداً: فشل الربط لا يجب أن يوقف اعتماد الطلب. */
    public function handleApprovedOrder(Order $order): ?SalesLead
    {
        if (! self::ready() || $order->status !== Order::STATUS_APPROVED) {
            return null;
        }

        try {
            if (SalesLead::query()->where('order_id', $order->id)->exists()) {
                return null;
            }
            $lead = $this->findLeadFor($order);

            return $lead ? $this->link($lead, $order, null) : null;
        } catch (\Throwable $e) {
            Log::warning('Sales: linking order to lead failed', ['order_id' => $order->id, 'message' => $e->getMessage()]);

            return null;
        }
    }

    /** ربط يدوي من الأدمن (عند تعذّر المطابقة التلقائية: رقم مختلف، شراء باسم شخص آخر…). */
    public function linkManually(SalesLead $lead, Order $order, User $admin): SalesLead
    {
        if ($order->status !== Order::STATUS_APPROVED) {
            throw ValidationException::withMessages(['order_id' => 'الطلب #'.$order->id.' غير معتمد بعد.']);
        }
        $other = SalesLead::query()->where('order_id', $order->id)->where('id', '!=', $lead->id)->first();
        if ($other) {
            throw ValidationException::withMessages(['order_id' => 'الطلب #'.$order->id.' مربوط بالفعل بـ Lead آخر (#'.$other->id.').']);
        }
        if ($lead->order_id && (int) $lead->order_id !== (int) $order->id) {
            throw ValidationException::withMessages(['order_id' => 'هذا الـ Lead مربوط بالفعل بالطلب #'.$lead->order_id.'.']);
        }

        return $this->link($lead, $order, $admin);
    }

    /**
     * المبلغ المدفوع فعلاً بعد الاستردادات (كلي أو جزئي).
     */
    public function netPaid(Order $order): float
    {
        $order->loadMissing('payment');
        if ($order->payment && $order->payment->status === 'refunded') {
            return 0.0;
        }
        $refunded = 0.0;
        if ($order->payment_id) {
            $refunded = (float) Transaction::query()
                ->where('payment_id', $order->payment_id)
                ->where('category', 'refund')
                ->where('status', 'completed')
                ->sum('amount');
        }

        return max(0.0, round((float) $order->amount - $refunded, 2));
    }

    public function findLeadFor(Order $order): ?SalesLead
    {
        $student = $order->user;
        if (! $student) {
            return null;
        }
        $tail = PhoneKey::likeTail($student->phone);
        $email = trim((string) $student->email);
        $since = now()->subDays((int) config('sales.attribution_days', 90));

        $candidates = SalesLead::query()
            ->whereNull('order_id')
            ->whereNull('won_confirmed_at')
            ->where(function ($q) use ($student, $tail, $email) {
                $q->where('student_user_id', $student->id);
                if ($tail) {
                    $q->orWhere('phone', 'like', $tail);
                }
                if ($email !== '') {
                    $q->orWhere('email', $email);
                }
            })
            ->where(function ($q) use ($since) {
                $q->where('updated_at', '>=', $since)->orWhere('last_contacted_at', '>=', $since);
            })
            ->get()
            ->filter(fn (SalesLead $l) => (int) $l->student_user_id === (int) $student->id
                || PhoneKey::same($l->phone, $student->phone)
                || ($email !== '' && strcasecmp((string) $l->email, $email) === 0));

        // الأقرب للبيع أولاً: فوز بانتظار الاعتماد ← دفع ← عرض… ثم الأحدث تواصلاً
        $rank = [SalesLead::WON_STAGE => 0, 'payment_received' => 1, 'payment_pending' => 2, 'offer_sent' => 3, 'interested' => 4, 'follow_up_scheduled' => 5, 'objection' => 6, 'qualification' => 7, 'connected' => 8];

        return $candidates->sortBy([
            fn ($a, $b) => ($a->assigned_to ? 0 : 1) <=> ($b->assigned_to ? 0 : 1),
            fn ($a, $b) => ($rank[$a->stage] ?? 20) <=> ($rank[$b->stage] ?? 20),
            fn ($a, $b) => ($b->last_contacted_at ?? $b->updated_at) <=> ($a->last_contacted_at ?? $a->updated_at),
        ])->first();
    }

    private function link(SalesLead $lead, Order $order, ?User $admin): SalesLead
    {
        return DB::transaction(function () use ($lead, $order, $admin) {
            $lead = SalesLead::query()->whereKey($lead->id)->lockForUpdate()->firstOrFail();
            $from = $lead->stage;
            $net = $this->netPaid($order);

            $updates = [
                'order_id' => $order->id,
                'student_user_id' => $order->user_id,
                'order_linked_at' => now(),
                'expected_value' => $net,
                'payment_amount' => $order->amount,
                'paid_at' => $order->approved_at ?? now(),
            ];
            if (! $lead->linkedCourseId() && $order->advanced_course_id) {
                $lead->applyCourseSelection('advanced', (int) $order->advanced_course_id);
            }
            if (! in_array($lead->stage, SalesLead::WON_LIKE_STAGES, true)) {
                $updates['stage'] = SalesLead::WON_STAGE;
                $updates['stage_entered_at'] = now();
                $updates['closed_at'] = now();
            }
            $before = $lead->only(array_keys($updates));
            $lead->forceFill($updates)->save();

            SalesActivity::create([
                'sales_lead_id' => $lead->id,
                'user_id' => $admin?->id ?? $order->approved_by ?? $lead->assigned_to ?? $order->user_id,
                'type' => 'stage_change',
                'title' => 'ربط بطلب مدفوع #'.$order->id,
                'body' => 'تم ربط العميل بطلب معتمد بقيمة '.number_format($net, 2).' ج.م'
                    .($admin ? ' (ربط يدوي)' : ' (ربط تلقائي من اعتماد الطلب)'),
                'meta' => ['from' => $from, 'to' => $lead->stage, 'order_id' => $order->id, 'net_paid' => $net, 'manual' => (bool) $admin],
            ]);

            SalesAuditService::log('sales_lead_order_linked', $lead, $before, $lead->only(array_keys($updates)),
                'ربط Lead «'.($lead->name ?? '').'» بالطلب #'.$order->id);

            return $lead;
        });
    }
}
