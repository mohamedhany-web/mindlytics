<?php

namespace App\Domains\Advising\Services;

use App\Domains\Advising\Models\AdvisingAvailability;
use App\Domains\Advising\Models\AdvisingBooking;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesPlannedActivity;
use App\Domains\Sales\Services\SalesAuditService;
use App\Domains\Sales\Services\SalesLeadMovementPolicy;
use App\Domains\Sales\Services\SalesNotificationService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * ربط حجوزات الإرشاد بالـ CRM (docs/ADVISING.md):
 * - كل حجز = عميل محتمل. لو نفس الموبايل أو الإيميل موجود بيتربط بيه ويفضل مع صاحبه، وإلا بيتعمل عميل جديد من الموقع.
 * - صاحب العميل الجديد: مرشد الجلسة لو موظف مبيعات، وإلا الموظف المختار في إعدادات الإرشاد، وإلا طابور «من غير موظف».
 * - الجلسة بتظهر اجتماع مخطط في كاليندر صاحب العميل، ونتيجتها بتتسجّل في سجل العميل.
 * أي مشكلة في الربط ما بتوقفش الحجز نفسه (بتتسجّل في اللوج بس).
 */
class AdvisingCrmSync
{
    public function onBooked(AdvisingBooking $booking): void
    {
        if (! AdvisingSettings::get()['crm_sync'] || $booking->sales_lead_id) {
            return;
        }

        $this->safely(function () use ($booking) {
            DB::transaction(function () use ($booking) {
                $lead = $this->matchingLead($booking);
                $created = false;
                if (! $lead) {
                    $lead = SalesLead::create(app(SalesLeadMovementPolicy::class)->withCreateDefaults([
                        'assigned_to' => $this->assigneeFor($booking),
                        'name' => $booking->name,
                        'phone' => $booking->phone,
                        'email' => $booking->email,
                        'source' => 'website',
                        'stage' => 'new_lead',
                        'priority' => 'high', // حجز جلسة = عميل مهتم
                        'interest' => $booking->interest,
                        'notes' => $this->summary($booking),
                        'next_follow_up_at' => $booking->starts_at,
                        'follow_up_channel' => 'meeting',
                    ]));
                    $created = true;
                } else {
                    // عميل قديم: لو الجلسة قبل متابعته الجاية تبقى هي المتابعة
                    if (! $lead->next_follow_up_at || $booking->starts_at->lt($lead->next_follow_up_at)) {
                        $lead->update(['next_follow_up_at' => $booking->starts_at, 'follow_up_channel' => 'meeting']);
                    }
                    if (blank($lead->interest) && filled($booking->interest)) {
                        $lead->update(['interest' => $booking->interest]);
                    }
                }

                $actor = $this->actorFor($lead);
                if ($actor) {
                    SalesActivity::create([
                        'sales_lead_id' => $lead->id,
                        'user_id' => $actor->id,
                        'type' => 'note',
                        'title' => 'حجز جلسة إرشاد مهني من الموقع',
                        'body' => $this->summary($booking),
                    ]);
                }

                $planned = null;
                if ($lead->assigned_to) {
                    $planned = SalesPlannedActivity::create([
                        'kind' => 'meeting',
                        'title' => 'جلسة إرشاد مهني — '.$booking->name,
                        'description' => $this->summary($booking),
                        'sales_lead_id' => $lead->id,
                        'student_user_id' => $lead->student_user_id ?? $booking->user_id,
                        'assigned_to' => $lead->assigned_to,
                        'starts_at' => $booking->starts_at,
                        'duration_minutes' => $booking->duration_minutes,
                        'priority' => 'high',
                        'status' => 'planned',
                    ]);
                }

                $booking->update(['sales_lead_id' => $lead->id, 'sales_planned_activity_id' => $planned?->id]);

                SalesAuditService::log($created ? 'sales_lead_created_advising' : 'sales_lead_advising_booked', $lead, null,
                    ['advising_booking_id' => $booking->id, 'starts_at' => $booking->starts_at->toDateTimeString()],
                    ($created ? 'عميل جديد من حجز إرشاد مهني — ' : 'حجز إرشاد مهني لعميل موجود — ').$lead->name);

                if ($created && $lead->assigned_to) {
                    app(SalesNotificationService::class)->notifyLeadAssigned($lead->fresh());
                }
            });
        });
    }

    /** بعد ما الإدارة تعدّل الحجز: الإلغاء بيلغي الاجتماع، والنتيجة بتتسجّل في سجل العميل. */
    public function onUpdated(AdvisingBooking $booking, array $before): void
    {
        if (! $booking->sales_lead_id) {
            return;
        }

        $this->safely(function () use ($booking, $before) {
            $lead = SalesLead::query()->find($booking->sales_lead_id);
            if (! $lead) {
                return;
            }
            $planned = $booking->sales_planned_activity_id ? SalesPlannedActivity::query()->find($booking->sales_planned_activity_id) : null;

            // المرشد الجديد موظف مبيعات والعميل لسه من غير موظف: يستلمه
            if (! $lead->assigned_to && $booking->advisor_id && $this->isSalesStaff($booking->advisor_id)) {
                $lead->update(['assigned_to' => $booking->advisor_id]);
                app(SalesNotificationService::class)->notifyLeadAssigned($lead->fresh());
            }

            if ($planned && $planned->status === 'planned') {
                match ($booking->status) {
                    'cancelled' => $planned->update(['status' => 'cancelled']),
                    'no_show' => $planned->update(['status' => 'missed', 'completed_at' => now(), 'completed_by' => auth()->id()]),
                    'done' => $planned->update(['status' => 'done', 'completed_at' => now(), 'completed_by' => auth()->id(), 'result_notes' => $booking->outcome]),
                    default => null,
                };
            }

            $changedResult = ($before['outcome'] ?? null) !== $booking->outcome || ($before['recommended_program'] ?? null) !== $booking->recommended_program;
            $becameDone = ($before['status'] ?? null) !== 'done' && $booking->status === 'done';
            if (($becameDone || ($booking->status === 'done' && $changedResult)) && (filled($booking->outcome) || filled($booking->recommended_program))) {
                $actor = auth()->user() ?? $this->actorFor($lead);
                if ($actor) {
                    SalesActivity::create([
                        'sales_lead_id' => $lead->id,
                        'user_id' => $actor->id,
                        'type' => 'meeting',
                        'title' => 'نتيجة جلسة الإرشاد المهني',
                        'body' => trim(
                            (filled($booking->recommended_program) ? 'البرنامج المرشَّح: '.$booking->recommended_program."\n" : '')
                            .($booking->outcome ?? '')
                        ),
                    ]);
                }
                if (filled($booking->recommended_program)) {
                    $lead->update(['interest' => $booking->recommended_program]);
                }
            }
            if (($before['status'] ?? null) !== $booking->status && in_array($booking->status, ['cancelled', 'no_show'], true)) {
                $actor = auth()->user() ?? $this->actorFor($lead);
                if ($actor) {
                    SalesActivity::create([
                        'sales_lead_id' => $lead->id,
                        'user_id' => $actor->id,
                        'type' => 'note',
                        'title' => $booking->status === 'cancelled' ? 'اتلغت جلسة الإرشاد المهني' : 'ما حضرش جلسة الإرشاد المهني',
                        'body' => 'ميعاد الجلسة كان '.$booking->starts_at->format('Y-m-d H:i').'.',
                    ]);
                }
            }
        });
    }

    /** نفس الموبايل (بأشكاله) أو نفس الإيميل. */
    private function matchingLead(AdvisingBooking $booking): ?SalesLead
    {
        $digits = preg_replace('/\D+/', '', (string) $booking->phone) ?? '';
        $variants = array_values(array_unique(array_filter([
            $booking->phone, $digits, ltrim($digits, '0'),
            str_starts_with($digits, '20') ? '0'.substr($digits, 2) : null,
            str_starts_with($digits, '20') ? '+'.$digits : null,
            str_starts_with($digits, '0') ? '20'.substr($digits, 1) : null,
            str_starts_with($digits, '0') ? '+20'.substr($digits, 1) : null,
        ])));
        $email = filled($booking->email) ? strtolower(trim((string) $booking->email)) : null;

        return SalesLead::query()
            ->where(function ($q) use ($variants, $email) {
                $q->whereIn('phone', $variants);
                if ($email) {
                    $q->orWhereRaw('LOWER(TRIM(email)) = ?', [$email]);
                }
            })
            ->orderByDesc('id')->first();
    }

    private function assigneeFor(AdvisingBooking $booking): ?int
    {
        if ($booking->advisor_id && $this->isSalesStaff($booking->advisor_id)) {
            return (int) $booking->advisor_id;
        }
        $configured = AdvisingSettings::get()['crm_assignee_id'];

        return $configured && $this->isSalesStaff($configured) ? $configured : null;
    }

    private function isSalesStaff(int $userId): bool
    {
        $user = User::query()->where('is_active', true)->find($userId);

        return $user !== null && (User::salesEmployees()->whereKey($userId)->exists() || $user->hasSalesManagerPortalAccess());
    }

    /** اللي هيتكتب باسمه النشاط: صاحب العميل، وإلا أول أدمن (عشان النشاط لازم يكون باسم حد). */
    private function actorFor(SalesLead $lead): ?User
    {
        return ($lead->assigned_to ? User::query()->find($lead->assigned_to) : null)
            ?? User::query()->where('is_active', true)->whereIn('role', ['super_admin', 'admin'])->orderBy('id')->first();
    }

    private function summary(AdvisingBooking $booking): string
    {
        $day = AdvisingAvailability::WEEKDAYS[$booking->starts_at->dayOfWeek] ?? '';

        return trim(implode("\n", array_filter([
            'جلسة إرشاد مهني: '.$day.' '.$booking->starts_at->format('Y-m-d H:i').' ('.$booking->duration_minutes.' دقيقة، '.AdvisingBooking::label(AdvisingBooking::MODES, $booking->mode).')',
            'الهدف: '.AdvisingBooking::label(AdvisingBooking::GOALS, $booking->goal),
            $booking->level ? 'المستوى: '.AdvisingBooking::label(AdvisingBooking::LEVELS, $booking->level) : null,
            filled($booking->interest) ? 'مهتم بـ: '.$booking->interest : null,
            filled($booking->notes) ? 'كلامه عن وضعه: '.$booking->notes : null,
            '[advising_booking:'.$booking->id.']',
        ])));
    }

    private function safely(callable $fn): void
    {
        try {
            $fn();
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
