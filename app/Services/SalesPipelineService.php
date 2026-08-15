<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesPipelineService
{
    /**
     * مراحل الحجز/الدفع — يلزم ربط كورس أو دبلومة قبلها.
     *
     * @return list<string>
     */
    public function bookingStages(): array
    {
        return ['payment_pending', 'payment_received', SalesLead::WON_STAGE];
    }

    /**
     * نتائج مسموحة من المرحلة الحالية.
     * العميل ممكن يحجز من أول مكالمة أو من الشات بعدين — مش لازم يعدّي كل خطوة.
     *
     * @return list<string>
     */
    public function allowedNextStages(SalesLead $lead): array
    {
        $close = ['payment_pending', 'payment_received', SalesLead::WON_STAGE];
        $progress = ['qualification', 'interested', 'offer_sent', 'follow_up_scheduled', 'objection'];

        return match ($lead->stage) {
            'new_lead' => array_values(array_unique(['first_contact', 'connected', 'no_answer', ...$progress, ...$close, 'lost'])),
            'first_contact' => array_values(array_unique(['connected', 'no_answer', ...$progress, ...$close, 'lost'])),
            'no_answer' => array_values(array_unique(['no_answer', 'connected', 'first_contact', ...$progress, ...$close, 'dormant', 'lost'])),
            'connected' => array_values(array_unique([...$progress, ...$close, 'lost'])),
            'qualification' => array_values(array_unique(['interested', 'offer_sent', 'follow_up_scheduled', 'objection', ...$close, 'lost'])),
            'interested' => array_values(array_unique(['offer_sent', 'follow_up_scheduled', 'objection', ...$close, 'lost'])),
            'objection' => array_values(array_unique(['follow_up_scheduled', 'interested', 'offer_sent', ...$close, 'lost'])),
            'follow_up_scheduled' => array_values(array_unique(['connected', 'qualification', 'interested', 'offer_sent', 'objection', ...$close, 'lost'])),
            'offer_sent' => array_values(array_unique([...$close, 'follow_up_scheduled', 'objection', 'lost'])),
            'payment_pending' => ['payment_received', SalesLead::WON_STAGE, 'follow_up_scheduled', 'lost'],
            'payment_received' => [SalesLead::WON_STAGE, 'lost'],
            'enrollment_completed' => ['upsell'],
            'upsell' => ['upsell'],
            'dormant' => array_values(array_unique(['first_contact', 'connected', ...$close, 'lost'])),
            'lost' => [],
            default => [],
        };
    }

    /**
     * أزرار النتيجة للموظف — مجموعات عملية مش مسار إجباري.
     *
     * @return list<array{stage: string, label: string, hint: string, group: string, tone: string}>
     */
    public function outcomeActions(SalesLead $lead): array
    {
        $allowed = $this->allowedNextStages($lead);
        $catalog = [
            'payment_pending' => ['label' => 'حجز — بانتظار الدفع', 'hint' => 'من المكالمة أو الشات', 'group' => 'close', 'tone' => 'success'],
            'payment_received' => ['label' => 'تم الدفع', 'hint' => 'حوّل / إنستاباي', 'group' => 'close', 'tone' => 'success'],
            'enrollment_completed' => ['label' => 'تسجيل مكتمل', 'hint' => 'اتقفلت الصفقة', 'group' => 'close', 'tone' => 'success'],
            'connected' => ['label' => 'تم الرد / تواصل', 'hint' => 'اتكلمنا', 'group' => 'contact', 'tone' => 'neutral'],
            'no_answer' => ['label' => 'ما ردّش', 'hint' => 'محاولة تانية', 'group' => 'contact', 'tone' => 'neutral'],
            'first_contact' => ['label' => 'أول تواصل', 'hint' => 'اتصلنا', 'group' => 'contact', 'tone' => 'neutral'],
            'interested' => ['label' => 'مهتم', 'hint' => 'لسه بيفكّر', 'group' => 'progress', 'tone' => 'neutral'],
            'qualification' => ['label' => 'تأهيل', 'hint' => 'بيانات أكتر', 'group' => 'progress', 'tone' => 'neutral'],
            'offer_sent' => ['label' => 'بعت عرض', 'hint' => 'سعر / تقسيط', 'group' => 'progress', 'tone' => 'neutral'],
            'follow_up_scheduled' => ['label' => 'هتابع لاحقاً', 'hint' => 'حدد الموعد هنا بس', 'group' => 'later', 'tone' => 'neutral'],
            'objection' => ['label' => 'اعتراض', 'hint' => 'سعر / وقت / أهل', 'group' => 'later', 'tone' => 'warn'],
            'lost' => ['label' => 'خسارة', 'hint' => 'قفّل الملف', 'group' => 'exit', 'tone' => 'danger'],
            'dormant' => ['label' => 'راكد', 'hint' => 'بعد 3 محاولات', 'group' => 'exit', 'tone' => 'danger'],
            'upsell' => ['label' => 'بيع إضافي', 'hint' => 'كورس تاني', 'group' => 'close', 'tone' => 'success'],
        ];

        $actions = [];
        foreach ($catalog as $stage => $meta) {
            if (! in_array($stage, $allowed, true)) {
                continue;
            }
            $actions[] = ['stage' => $stage] + $meta;
        }

        return $actions;
    }

    public function suggestedDefaultStage(SalesLead $lead): string
    {
        $allowed = $this->allowedNextStages($lead);
        $preferred = match ($lead->stage) {
            'new_lead', 'first_contact', 'no_answer', 'dormant' => ['connected', 'no_answer', 'payment_pending'],
            'connected', 'qualification' => ['interested', 'payment_pending', 'offer_sent'],
            'interested', 'offer_sent', 'objection', 'follow_up_scheduled' => ['payment_pending', 'follow_up_scheduled'],
            'payment_pending' => ['payment_received', SalesLead::WON_STAGE],
            'payment_received' => [SalesLead::WON_STAGE],
            default => [],
        };

        foreach ($preferred as $stage) {
            if (in_array($stage, $allowed, true)) {
                return $stage;
            }
        }

        return $allowed[0] ?? $lead->stage;
    }

    /**
     * قمع مختصر للعرض (7 مراحل بدل 15).
     *
     * @return array<string, string>
     */
    public function journeyBuckets(): array
    {
        return [
            'entered' => 'دخول',
            'contacted' => 'تواصل',
            'qualified' => 'تأهيل',
            'offer' => 'عرض',
            'payment' => 'حجز/دفع',
            'won' => 'تسجيل',
            'lost' => 'خروج',
        ];
    }

    /**
     * تجميع أعداد العملاء حسب القمع المختصر.
     *
     * @param  array<string, int>  $stageCounts  stage => count
     * @return array<string, int>
     */
    public function bucketCountsFromStageCounts(array $stageCounts): array
    {
        $buckets = array_fill_keys(array_keys($this->journeyBuckets()), 0);
        foreach ($stageCounts as $stage => $count) {
            $bucket = $this->bucketForStage((string) $stage);
            $buckets[$bucket] = ($buckets[$bucket] ?? 0) + (int) $count;
        }

        return $buckets;
    }

    /**
     * @return list<string>
     */
    public function suggestedNextStages(SalesLead $lead): array
    {
        return $this->allowedNextStages($lead);
    }

    public function bucketForStage(string $stage): string
    {
        $stage = SalesLead::normalizeStage($stage);

        return match ($stage) {
            'new_lead' => 'entered',
            'first_contact', 'no_answer', 'connected' => 'contacted',
            'qualification', 'interested', 'objection', 'follow_up_scheduled' => 'qualified',
            'offer_sent' => 'offer',
            'payment_pending', 'payment_received' => 'payment',
            'enrollment_completed', 'upsell' => 'won',
            'lost', 'dormant' => 'lost',
            default => 'entered',
        };
    }

    /**
     * @return array<string, string>
     */
    public function requiredFieldsFor(string $stage): array
    {
        return match ($stage) {
            'first_contact' => [
                'call_answered' => 'هل تم الرد؟',
            ],
            'connected' => [
                'connected_disposition' => 'نتيجة الاتصال',
            ],
            'qualification' => [
                // كل حقول التأهيل اختيارية — الملاحظات العامة للانتقال كافية
            ],
            'interested' => [
                'interest_pct' => 'نسبة الاهتمام',
            ],
            'objection' => [
                'objection_reason' => 'سبب الاعتراض',
            ],
            'follow_up_scheduled' => [
                'next_follow_up_at' => 'موعد المتابعة',
                'follow_up_channel' => 'طريقة التواصل',
            ],
            'offer_sent' => [
                'offer_price' => 'السعر المعروض',
            ],
            'payment_pending' => [
                'payment_method' => 'طريقة الدفع',
                'payment_amount' => 'قيمة الدفع',
                'payment_due_at' => 'تاريخ الاستحقاق',
            ],
            'payment_received' => [
                'payment_txn_ref' => 'رقم العملية',
                'payment_amount' => 'المبلغ',
                'paid_at' => 'تاريخ الدفع',
            ],
            'enrollment_completed' => [
                'expected_value' => 'قيمة الصفقة',
            ],
            'lost' => [
                'lost_reason' => 'سبب الخسارة',
            ],
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function transition(SalesLead $lead, string $toStage, array $payload, User $actor): SalesLead
    {
        $toStage = SalesLead::normalizeStage($toStage);
        if (! array_key_exists($toStage, SalesLead::STAGES)) {
            throw ValidationException::withMessages(['stage' => 'مرحلة غير معروفة.']);
        }

        if ($toStage === 'first_contact' && array_key_exists('call_answered', $payload)
            && ! filter_var($payload['call_answered'], FILTER_VALIDATE_BOOLEAN)) {
            $toStage = 'no_answer';
        }

        if ($toStage === $lead->stage && $toStage !== 'no_answer') {
            throw ValidationException::withMessages(['stage' => 'العميل بالفعل في هذه المرحلة.']);
        }

        $allowed = $this->allowedNextStages($lead);
        if ($toStage !== $lead->stage && ! in_array($toStage, $allowed, true)) {
            throw ValidationException::withMessages([
                'stage' => ['انتقال غير مسموح من المرحلة الحالية. الخيارات المتاحة: '
                    .collect($allowed)->map(fn ($s) => SalesLead::stageLabel($s))->implode(' · ')],
            ]);
        }

        $this->assertTransitionNotes($payload);
        $this->assertRequired($toStage, $payload);
        $this->applyCourseFromPayload($lead, $payload);
        $this->assertCourseLinkedForBooking($lead, $toStage);

        return DB::transaction(function () use ($lead, $toStage, $payload, $actor) {
            $from = $lead->stage;
            $updates = $this->buildUpdates($toStage, $payload);
            $this->ensureMovementDefaults($lead, $toStage, $updates, $payload);

            if ($toStage === 'no_answer') {
                $this->applyNoAnswerBump($lead, $updates);
                $toStage = $updates['stage'];
            }

            if ($toStage === 'connected') {
                $updates['last_contacted_at'] = now();
                if (($payload['connected_disposition'] ?? null) === 'wrong_number') {
                    $toStage = 'lost';
                    $updates['stage'] = 'lost';
                    $updates['lost_reason'] = 'wrong_number';
                    $updates['closed_at'] = now();
                }
            }

            if (in_array($toStage, ['first_contact', 'qualification', 'interested', 'offer_sent', 'payment_pending', 'payment_received'], true)) {
                $updates['last_contacted_at'] = $updates['last_contacted_at'] ?? now();
            }

            $updates['stage'] = $toStage;
            $updates['stage_entered_at'] = now();

            if (in_array($toStage, SalesLead::CLOSED_STAGES, true) || $toStage === SalesLead::WON_STAGE) {
                $updates['closed_at'] = $updates['closed_at'] ?? now();
            } elseif (! in_array($toStage, SalesLead::WON_LIKE_STAGES, true)) {
                $updates['closed_at'] = null;
            }

            app(SalesLeadMovementPolicy::class)->assertOpenLeadHasMovement(
                array_merge([
                    'stage' => $lead->stage,
                    'next_follow_up_at' => $lead->next_follow_up_at,
                    'follow_up_channel' => $lead->follow_up_channel,
                ], $updates),
                $lead
            );

            $before = $lead->only(array_keys($updates));
            $lead->fill($updates);
            $lead->save();

            $duration = isset($payload['duration_seconds']) && $payload['duration_seconds'] !== ''
                ? (int) $payload['duration_seconds']
                : null;
            $recording = isset($payload['recording_url']) && $payload['recording_url'] !== ''
                ? (string) $payload['recording_url']
                : null;

            SalesActivity::create([
                'sales_lead_id' => $lead->id,
                'user_id' => $actor->id,
                'type' => 'stage_change',
                'duration_seconds' => $duration,
                'recording_url' => $recording,
                'title' => 'انتقال: '.SalesLead::stageLabel($from).' ← '.SalesLead::stageLabel($toStage),
                'body' => $payload['notes'] ?? $payload['objection_notes'] ?? null,
                'meta' => [
                    'from' => $from,
                    'to' => $toStage,
                    'fields' => collect($payload)->except(['_token'])->filter(fn ($v) => $v !== null && $v !== '')->all(),
                    'duration_seconds' => $duration,
                    'recording_url' => $recording,
                    'contact_attempts' => $lead->contact_attempts,
                ],
            ]);

            SalesAuditService::log(
                'sales_lead_pipeline_transition',
                $lead,
                $before,
                $lead->only(array_keys($updates)),
                'تحديث مرحلة Pipeline: '.$from.' → '.$toStage
            );

            if ($toStage === SalesLead::WON_STAGE && $from !== SalesLead::WON_STAGE) {
                try {
                    app(SalesNotificationService::class)->notifyWinPendingApproval($lead->fresh(['assignee']));
                } catch (\Throwable) {
                    // ignore
                }
            }

            return $lead->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function assertTransitionNotes(array $payload): void
    {
        $notes = trim((string) ($payload['notes'] ?? ''));
        if (mb_strlen($notes) < 5) {
            throw ValidationException::withMessages([
                'notes' => ['الملاحظات إلزامية عند كل انتقال (5 أحرف على الأقل) — اكتب ماذا حصل باختصار.'],
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function applyCourseFromPayload(SalesLead $lead, array $payload): void
    {
        $type = $payload['course_type'] ?? null;
        $ref = $payload['course_ref_id'] ?? null;
        if ($type && $ref) {
            $lead->applyCourseSelection((string) $type, (int) $ref);
            $lead->save();
        }
    }

    private function assertCourseLinkedForBooking(SalesLead $lead, string $toStage): void
    {
        if (! in_array($toStage, $this->bookingStages(), true)) {
            return;
        }

        $lead->refresh();
        if (! $lead->linkedCourseId()) {
            throw ValidationException::withMessages([
                'course_ref_id' => ['قبل الحجز/الدفع لازم تربط العميل بكورس أو دبلومة معيّنة من الكتالوج.'],
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function assertRequired(string $stage, array $payload): void
    {
        if ($stage === 'first_contact' && ! array_key_exists('call_answered', $payload)) {
            throw ValidationException::withMessages(['call_answered' => 'حدّد هل تم الرد أم لا.']);
        }

        if ($stage === 'no_answer') {
            return;
        }

        foreach ($this->requiredFieldsFor($stage) as $key => $label) {
            if ($key === 'call_answered') {
                continue;
            }
            if ($key === 'can_pay') {
                if (! array_key_exists('can_pay', $payload) || $payload['can_pay'] === '' || $payload['can_pay'] === null) {
                    throw ValidationException::withMessages([$key => $label.' مطلوب.']);
                }

                continue;
            }

            $val = $payload[$key] ?? null;
            if ($val === null || $val === '') {
                throw ValidationException::withMessages([$key => $label.' مطلوب.']);
            }
        }

        if ($stage === 'interested') {
            $pct = (int) ($payload['interest_pct'] ?? 0);
            if (! in_array($pct, SalesLead::INTEREST_PCTS, true)) {
                throw ValidationException::withMessages(['interest_pct' => 'نسبة اهتمام غير صالحة.']);
            }
        }

        if ($stage === 'connected' && ! array_key_exists(($payload['connected_disposition'] ?? ''), SalesLead::CONNECTED_DISPOSITIONS)) {
            throw ValidationException::withMessages(['connected_disposition' => 'نتيجة الاتصال غير صالحة.']);
        }

        if ($stage === 'objection' && ! array_key_exists(($payload['objection_reason'] ?? ''), SalesLead::OBJECTION_REASONS)) {
            throw ValidationException::withMessages(['objection_reason' => 'سبب الاعتراض غير صالح.']);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function buildUpdates(string $toStage, array $payload): array
    {
        $updates = ['stage' => $toStage];

        foreach ([
            'connected_disposition', 'profile_type', 'age', 'age_range', 'field_domain', 'experience_level',
            'course_motivation', 'start_preference', 'interest_pct', 'objection_reason',
            'objection_notes', 'follow_up_channel', 'offer_discount', 'offer_installment_plan',
            'offer_notes', 'payment_method', 'payment_txn_ref', 'lost_reason',
        ] as $key) {
            if (array_key_exists($key, $payload) && $payload[$key] !== null && $payload[$key] !== '') {
                $updates[$key] = $payload[$key];
            }
        }

        if (! empty($payload['age_range']) && array_key_exists((string) $payload['age_range'], SalesLead::AGE_RANGES)) {
            $updates['age_range'] = (string) $payload['age_range'];
            $mid = SalesLead::ageRangeMidpoint((string) $payload['age_range']);
            if ($mid !== null) {
                $updates['age'] = $mid;
            }
        }

        if (array_key_exists('can_pay', $payload) && $payload['can_pay'] !== null && $payload['can_pay'] !== '') {
            $updates['can_pay'] = filter_var($payload['can_pay'], FILTER_VALIDATE_BOOLEAN);
        }

        if (! empty($payload['next_follow_up_at'])) {
            $updates['next_follow_up_at'] = Carbon::parse($payload['next_follow_up_at']);
        }

        if (! empty($payload['offer_price'])) {
            $updates['offer_price'] = $payload['offer_price'];
            $updates['expected_value'] = $payload['offer_price'];
        }

        if (! empty($payload['expected_value'])) {
            $updates['expected_value'] = $payload['expected_value'];
        }

        if ($toStage === 'offer_sent') {
            $updates['offer_sent_at'] = now();
        }

        if (! empty($payload['payment_amount'])) {
            $updates['payment_amount'] = $payload['payment_amount'];
            if (empty($updates['expected_value'])) {
                $updates['expected_value'] = $payload['payment_amount'];
            }
        }

        if (! empty($payload['payment_due_at'])) {
            $updates['payment_due_at'] = Carbon::parse($payload['payment_due_at']);
            if ($toStage === 'payment_pending' && empty($payload['next_follow_up_at'])) {
                $updates['next_follow_up_at'] = $updates['payment_due_at'];
                $updates['follow_up_channel'] = $updates['follow_up_channel'] ?? 'whatsapp';
            }
        }

        if (! empty($payload['paid_at'])) {
            $updates['paid_at'] = Carbon::parse($payload['paid_at']);
        } elseif ($toStage === 'payment_received') {
            $updates['paid_at'] = now();
        }

        return $updates;
    }

    /**
     * @param  array<string, mixed>  $updates
     */
    private function applyNoAnswerBump(SalesLead $lead, array &$updates): void
    {
        $attempt = (int) $lead->contact_attempts + 1;
        $updates['contact_attempts'] = $attempt;
        $updates['last_attempt_at'] = now();
        $updates['last_contacted_at'] = now();

        if ($attempt >= 3) {
            $updates['stage'] = 'dormant';
            $updates['next_attempt_due_at'] = null;
            $updates['closed_at'] = now();
        } else {
            $updates['stage'] = 'no_answer';
            $due = $attempt === 1
                ? now()->addHours(2)
                : now()->addDay()->startOfDay()->setTime(10, 0);
            $updates['next_attempt_due_at'] = $due;
            $updates['next_follow_up_at'] = $due;
            $updates['follow_up_channel'] = $updates['follow_up_channel'] ?? 'call';
        }
    }

    /**
     * المتابعة إلزامية فقط لما النتيجة «هتابع لاحقاً».
     * باقي النقلات: نكمّل معاد موجود، أو بكرة 10ص واتساب من غير ما الموظف يملأ الحقل كل مرة.
     *
     * @param  array<string, mixed>  $updates
     * @param  array<string, mixed>  $payload
     */
    private function ensureMovementDefaults(SalesLead $lead, string $toStage, array &$updates, array $payload): void
    {
        if ($toStage === 'follow_up_scheduled') {
            return;
        }

        if (! app(SalesLeadMovementPolicy::class)->requiresActiveMovement($toStage)) {
            return;
        }

        $hasFollow = ! empty($updates['next_follow_up_at']) || ! empty($payload['next_follow_up_at']);
        $existingFollow = $lead->next_follow_up_at;
        $existingIsFuture = $existingFollow instanceof Carbon && $existingFollow->isFuture();

        if (! $hasFollow && ! $existingIsFuture) {
            $updates['next_follow_up_at'] = now()->addDay()->setTime(10, 0);
        }

        $channel = $updates['follow_up_channel'] ?? $payload['follow_up_channel'] ?? $lead->follow_up_channel;
        if ($channel === null || $channel === '' || ! array_key_exists((string) $channel, SalesLead::FOLLOW_UP_CHANNELS)) {
            $updates['follow_up_channel'] = 'whatsapp';
        }
    }
}
